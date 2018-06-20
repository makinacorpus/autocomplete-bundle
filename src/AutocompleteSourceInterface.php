<?php

namespace MakinaCorpus\Autocomplete;

use MakinaCorpus\Calista\Query\Query;

/**
 * Autocomplete source for the TextAutocomplete widget
 *
 * An autocomplete source converts business class instances to whatever the
 * autocomplete widget needs:
 *
 *  - within the form, default form type values must be set to something this
 *    source understands;
 *
 *  - within the form, values returned after validation and transformation are
 *    instances of the same type;
 *
 *  - the find() method is heart of everything, it gives you the unescaped raw
 *    value the user typed, it's your job to sanitize it and search.
 */
interface AutocompleteSourceInterface
{
    /**
     * Find items
     *
     * @param Query $query
     *   Search query
     *
     * @return mixed[]
     *   Array of business class instances this source handles
     */
    public function find(Query $query): array;

    /**
     * Find a single item using its identifier
     *
     * You may throw exception if item could not be found, don't care
     * about the exception type, the widget will handle as much as it can and
     * provide meaningful exceptions for the fom validation process.
     *
     * @param int|string $id
     *   Object identifier
     *
     * @return mixed
     *   Single business class instance this source handles
     */
    public function findById(string $id);

    /**
     * Find all items using their identifiers
     *
     * You may throw exception if one or more items are not found, don't care
     * about the exception type, the widget will handle as much as it can and
     * provide meaningful exceptions for the fom validation process.
     *
     * @param int[]|string[] $idList
     *   Array of object identifiers
     *
     * @return mixed[]
     *   Array of business class instances this source handles
     */
    public function findAllById(array $idList): array;

    /**
     * Get item identifier
     *
     * You may throw exception in case of any error, don't care
     * about the exception type, the widget will handle as much as it can and
     * provide meaningful exceptions for the fom validation process.
     *
     * @param mixed $value
     *   An object loaded either by the find(), the findAllById() or the
     *   findById() method of this very same object
     *
     * @return int|string
     *   The object identifier
     */
    public function getItemId($value): string;

    /**
     * Get item display label
     *
     * You may throw exception in case of any error, don't care
     * about the exception type, the widget will handle as much as it can and
     * provide meaningful exceptions for the fom validation process.
     *
     * @param mixed $value
     *   An object loaded either by the find(), the findAllById() or the
     *   findById() method of this very same object
     *
     * @return string
     *   A textual representation of the object
     */
    public function getItemLabel($value): string;

    /**
     * Get item additional data
     *
     * You may throw exception in case of any error, don't care
     * about the exception type, the widget will handle as much as it can and
     * provide meaningful exceptions for the fom validation process.
     *
     * @param mixed $value
     *   An object loaded either by the find(), the findAllById() or the
     *   findById() method of this very same object
     *
     * @return []
     *   An array of data related to the object
     */
    public function getItemExtraData($value): array;

    /**
     * Render item markup to display within the autocomplete widget, it might
     * advanced HTML rendering
     *
     * You may throw exception in case of any error, don't care
     * about the exception type, the widget will handle as much as it can and
     * provide meaningful exceptions for the fom validation process.
     *
     * @param mixed $value
     *   An object loaded either by the find(), the findAllById() or the
     *   findById() method of this very same object
     *
     * @return string
     *   HTML safe output that represent the object
     */
    public function renderItemMarkup($value): string;
}
