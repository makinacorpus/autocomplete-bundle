<?php

namespace MakinaCorpus\AutocompleteBundle\Form\Type;

use MakinaCorpus\AutocompleteBundle\Autocomplete\AutocompleteSourceInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TextAutocompleteDataTransformer implements DataTransformerInterface
{
    private $source;
    /**
     * @var
     */
    private $options;

    /**
     * Default constructor
     *
     * @param AutocompleteSourceInterface|\Doctrine\ORM\EntityRepository $source
     * @param $options
     */
    public function __construct(AutocompleteSourceInterface $source, $options)
    {
        $this->source = $source;
        $this->options = $options;
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
        if (empty($value)) return null;
        $append = isset($this->options['tags']) ? $this->options['tags'] : false;
        if (is_array($value)) {
            try {
                $result = $this->source->findAllById($value, $append);
                return $result;
            } catch (\Exception $e) {
                throw new TransformationFailedException("One or more items does not exist", null, $e);
            }
        } else {
            // We are working with multiple values
            try {
                $entity = $this->source->findById($value) ?: $this->source->newEntity($value);
                return $entity;
            } catch (\Exception $e) {
                throw new TransformationFailedException("Item does not exist", null, $e);
            }
        }
    }
}
