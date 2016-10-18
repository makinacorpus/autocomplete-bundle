<?php

namespace MakinaCorpus\AutocompleteBundle\Autocomplete;

/**
 * Autocomplete source for the TextAutocomplete widget
 */
interface AutocompleteSourceInterface
{
    /**
     * Find items
     *
     * @param string $string
     *   Search string, unescaped user input
     * @param integer $limit
     *   Number of items to fetch
     * @param integer $offset
     *   Where to start (this is an offset, not a page)
     *
     * @return mixed[]
     *   Can be anything, really
     */
    public function find($string, $limit = 30, $offset = 0);

    /**
     * Find a single item using its identifier
     *
     * You may throw exception if the item does not exist
     *
     * @param mixed $id
     *
     * @return mixed
     *   Single loaded item, if not found null
     */
    public function findById($id);

    /**
     * Find all items using their identifiers
     *
     * You may throw exception if one or more items are not found
     *
     * @param int[]|string[] $idList
     *
     * @return mixed[]
     *   Items array, whose keys are the identifiers
     */
    public function findAllById($idList);

    /**
     * Get item identifier
     *
     * @param mixed $value
     *
     * @return int|string
     */
    public function getItemId($value);

    /**
     * Get item display label
     *
     * @param mixed $value
     *
     * @return string
     */
    public function getItemLabel($value);
}
