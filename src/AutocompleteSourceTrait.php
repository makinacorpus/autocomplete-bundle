<?php

declare(strict_types=1);

namespace MakinaCorpus\Autocomplete;

/**
 * Base implementation for lazy people.
 */
trait AutocompleteSourceTrait /* implemetns AutocompleteSourceInterface */
{
    /**
     * {@inheritdoc}
     */
    public function findById(string $id)
    {
        $items = $this->findAllById([$id]);

        if (!$items) {
            throw new \InvalidArgumentException(sprintf("object with id %s does not exist", $id));
        }

        return reset($items);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemLabel($value): string
    {
        return $this->getItemId($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemExtraData($value): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function renderItemMarkup($value): string
    {
        return $this->getItemLabel($value);
    }
}
