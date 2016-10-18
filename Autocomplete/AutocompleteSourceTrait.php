<?php

namespace MakinaCorpus\AutocompleteBundle\Autocomplete;

/**
 * Base implementation for lazzy people
 */
trait AutocompleteSourceTrait /* implemetns AutocompleteSourceInterface */
{
    /**
     * {@inheritdoc}
     */
    public function findById($id)
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
    public function getItemLabel($value)
    {
        return $this->getItemId($value);
    }

    /**
     * {@inheritdoc}
     */
    public function renderItemMarkup($value)
    {
        return $this->getItemLabel($value);
    }
}
