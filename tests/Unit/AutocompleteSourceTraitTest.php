<?php

namespace MakinaCorpus\Autocomplete\Tests\Unit;

use PHPUnit\Framework\TestCase;
use MakinaCorpus\Autocomplete\Tests\Mock\MockAutocompleteSource;
use MakinaCorpus\Autocomplete\Tests\Mock\MockItem;

class AutocompleteSourceTraitTest extends TestCase
{
    public function testAll()
    {
        $source = new MockAutocompleteSource();

        // Ensure that, per default, getItemLabel() returns the identifier
        $item = new MockItem("11", "Superbe label");
        $this->assertSame("11", $source->getItemLabel($item));

        // findById() calls findAllById()
        $newItem = $source->findById("Cassoulet");
        $this->assertSame("Cassoulet", $source->getItemId($newItem));

        // Per default, item label, and item markup are item identifier
        $this->assertSame("Cassoulet", $source->getItemLabel($newItem));
        $this->assertSame("Cassoulet", $source->renderItemMarkup($newItem));

        // Per default, item extra data is empty
        $this->assertEmpty($source->getItemExtraData($newItem));

        $source->toggleGeneration(false);
        try {
            $source->findById("Another identifier");
            $this->fail("It should have thrown an exception");
        } catch (\InvalidArgumentException $e) {}
    }
}
