<?php

namespace MakinaCorpus\Autocomplete\Bundle\Form\Type;

use MakinaCorpus\Autocomplete\AutocompleteSourceInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class AutocompleteDataTransformer implements DataTransformerInterface
{
    private AutocompleteSourceInterface $source;

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

        try {
            return $this->source->getItemLabel($value);
        } catch (\Throwable $e) {
            throw new TransformationFailedException("Data tranform failed", $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        try {
            return $this->source->findById($value);
        } catch (\Throwable $e) {
            throw new TransformationFailedException("Data tranform failed", $e->getCode(), $e);
        }
    }
}
