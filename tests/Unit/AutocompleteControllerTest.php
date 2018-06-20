<?php

namespace MakinaCorpus\Autocomplete\Tests\Unit;

use MakinaCorpus\Autocomplete\AutocompleteController;
use MakinaCorpus\Autocomplete\Tests\Mock\MockAutocompleteSource;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class AutocompleteControllerTest extends TestCase
{
    public function testAll()
    {
        $controller = new AutocompleteController('search', 'how_many', 'page_number');
        $source = new MockAutocompleteSource();
        $source->toggleGeneration(false);

        $request = new Request([
            'search' => 'bar',
            'how_many' => 12,
            'page_number' => 4,
        ]);
        $items = $controller->findArray($request, $source);

        $this->assertEmpty($items);

        $request = new Request([
            'search' => 'some',
            'how_many' => 12,
            'page_number' => 4,
        ]);
        $items = $controller->findArray($request, $source);
        $this->assertCount(12, $items);
        // First identifier must be 3 * 12 + 1
        // Last identifier must be 3 * 12 + 12
        $this->assertEquals(37, \array_shift($items)->id);
        $this->assertEquals(48, \array_pop($items)->id);

        // And fetch as JSON for fun
        $response = $controller->findJson($request, $source);
        $contents = \json_decode($response->getContent(), true);
        $this->assertEquals(12, $contents['limit']);
        $this->assertEquals(4, $contents['page']);
        $this->assertEquals(12, $contents['total']);
        $this->assertEquals(37, $contents['items'][0]['id']);
        $this->assertEquals(48, $contents['items'][11]['id']);
    }
}
