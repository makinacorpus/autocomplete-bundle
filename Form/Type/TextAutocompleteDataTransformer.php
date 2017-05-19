<?php

namespace MakinaCorpus\AutocompleteBundle\Form\Type;

use MakinaCorpus\AutocompleteBundle\Autocomplete\AutocompleteSourceInterface;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TextAutocompleteDataTransformer implements DataTransformerInterface
{
    private $source;

    /**
     * Default constructor
     *
     * @param AutocompleteSourceInterface $source
     */
    public function __construct(AutocompleteSourceInterface $source)
    {
        $this->source = $source;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value || '' === $value) {
            return $value;
        }

        if (is_array($value)) { // We are working with multiple values
            $ret = [];

            foreach ($value as $item) {
                $ret[$this->source->getId($item)] = $this->source->getLabel($item);
            }

            return $ret;

        } else {
            return $this->source->getLabel($value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (is_array($value)) { // We are working with multiple values
            try {
                return $this->source->findAllById($value);
            } catch (\Exception $e) {
                throw new TransformationFailedException("One or more items does not exist", null, $e);
            }
        } else {
            try {
                return $this->source->find($value);
            } catch (\Exception $e) {
                throw new TransformationFailedException("Item does not exist", null, $e);
            }
        }
    }
}
