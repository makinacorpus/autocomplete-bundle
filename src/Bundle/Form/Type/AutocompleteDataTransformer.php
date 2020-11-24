<?php

namespace MakinaCorpus\Autocomplete\Bundle\Form\Type;

use MakinaCorpus\Autocomplete\AutocompleteSourceInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class AutocompleteDataTransformer implements DataTransformerInterface
{
    private AutocompleteSourceInterface $source;
    private bool $allowArbitraryInput;
    private bool $allowValidationBypass;

    public function __construct(
        AutocompleteSourceInterface $source,
        bool $allowArbitraryInput,
        bool $allowValidationBypass
    ) {
        $this->allowArbitraryInput = $allowArbitraryInput;
        $this->allowValidationBypass = $allowValidationBypass;
        $this->source = $source;
    }

    /**
     * {@inheritdoc}
     *
     * Input is always a string.
     */
    public function transform($value)
    {
        if (null === $value || '' === $value) {
            return null;
        }

        try {
            $id = $this->getIdFromLabel($value);
            $item = $this->source->findById($id);

            if ($item) {
                return [
                    'label' => $this->source->getItemLabel($item),
                    'id' => $this->source->getItemId($item),
                ];
            } else {
                // This should not happen, but PHP type system does not support
                // generics, so we cannot enforce that findById() will not
                // return null in case source was poorly implemented.
                return null;
            }
        } catch (\Throwable $e) {
            // For form display, always allow the user to see the value that
            // was provided from model, even if invalid. At the very least, it
            // will give the user a hint about what's inside.
            return ['label' => $value, 'id' => null];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if ($this->allowArbitraryInput) {
            $id = $value['id'] ?? $this->getIdFromLabel($value['label']);
        } else {
            $id = $value['id'];
        }

        if (!$id || empty($value['label'])) {
            return null;
        }

        try {
            $item = $this->source->findById($id);

            if ($item) {
                return $this->source->getItemId($item);
            } else {
                // This should not happen, but PHP type system does not support
                // generics, so we cannot enforce that findById() will not
                // return null in case source was poorly implemented.
                return null;
            }
        } catch (\Throwable $e) {
            if ($this->allowValidationBypass) {
                // In case of validation error, but validation is not mandatory,
                // return raw user input instead, as configured for.
                return $value['label'] ?? null;
            }

            throw new TransformationFailedException("Data tranform failed", $e->getCode(), $e);
        }
    }

    private function getIdFromLabel(string $value): string
    {
        $value = \trim($value);

        return \strpos($value, ' ') ? \explode(' ', $value, 2)[0] : $value;
    }
}
