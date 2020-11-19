<?php

declare(strict_types=1);

namespace MakinaCorpus\Autocomplete\Tests\Bundle;

use MakinaCorpus\Autocomplete\AutocompleteController;
use MakinaCorpus\Autocomplete\Bundle\DependencyInjection\SourceRegistry;
use MakinaCorpus\Autocomplete\Tests\Mock\MockAutocompleteSource;
use MakinaCorpus\Autocomplete\Tests\Mock\MockUrlGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpFoundation\Request;

final class ControllerTest extends TestCase
{
    public function testFindMethod()
    {
        $container = new ContainerBuilder();
        $container->addDefinitions(['mock_source' => (new Definition(MockAutocompleteSource::class))->setPublic(true)]);
        $container->compile();

        $controller = new AutocompleteController();
        $registry = new SourceRegistry($container, new MockUrlGenerator());
        $registry->registerAll([MockAutocompleteSource::class => 'mock_source']);

        // Real test
        $request = new Request(['query' => 'some', 'limit' => 12, 'page' => 4]);
        $response = $controller->find($registry, $request, MockAutocompleteSource::class);
        $contents = \json_decode($response->getContent(), true);
        $this->assertEquals(12, $contents['limit']);
        $this->assertEquals(4, $contents['page']);
        $this->assertEquals(12, $contents['total']);
        $this->assertEquals(37, $contents['items'][0]['id']);
        $this->assertEquals(48, $contents['items'][11]['id']);
    }
}
