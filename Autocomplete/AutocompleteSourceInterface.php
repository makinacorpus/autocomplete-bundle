<?php

namespace MakinaCorpus\AutocompleteBundle\Autocomplete;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Autocomplete source for the TextAutocomplete widget
 */
interface AutocompleteSourceInterface extends DataTransformerInterface
{
    /**
     * Find items
     *
     * @param string $string
     *   Search string, unescaped user input
     * @param integer $limit
     *   Number of items to fetch
     *
     * @return mixed[]
     *   Can be anything, really, that will be then transformed using the
     *   DataTransformerInterface methods
     */
    public function find($string, $limit = 16);
}
