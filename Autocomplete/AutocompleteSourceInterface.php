<?php

namespace MakinaCorpus\AutocompleteBundle\Autocomplete;

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
     * @param string $string
     *   Search string, unescaped user input
     * @param integer $limit
     *   Number of items to fetch
     * @param integer $offset
     *   Where to start (this is an offset, not a page)
     *
     * @return mixed[]
     *   Array of business class instances this source handles
     */
    public function autocomplete($string, $limit = 30, $offset = 0);

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
    public function findAllById($idList);

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
    public function getId($value);

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
    public function getLabel($value);

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
    public function getExtraData($value);

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
     * @param string $string Maybe needed for highlight
     *
     * @return string HTML safe output that represent the object
     * HTML safe output that represent the object
     */
    public function getMarkup($value, $string='');
}
