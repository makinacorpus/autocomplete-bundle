<?php

namespace MakinaCorpus\Autocomplete\Tests\Bundle;

use MakinaCorpus\Autocomplete\Bundle\AutocompleteBundle;
use MakinaCorpus\Autocomplete\Bundle\DependencyInjection\AutocompleteExtension;
use MakinaCorpus\Autocomplete\Tests\Mock\MockAutocompleteSourceWithTemplateEngine;
use MakinaCorpus\Autocomplete\Tests\Mock\MockTemplateEngine;
use MakinaCorpus\Autocomplete\Tests\Mock\MockUrlGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use MakinaCorpus\Autocomplete\Tests\Mock\MockItem;

class TemplateTest extends TestCase
{
    public function testTemplateEngineInjection()
    {
        $container  = new ContainerBuilder();
        $container->addDefinitions([
            'router' => new Definition(MockUrlGenerator::class),
            'templating' => new Definition(MockTemplateEngine::class),
            'mock_source' => (new Definition(MockAutocompleteSourceWithTemplateEngine::class))
                ->addTag('autocomplete.source')
                ->setPublic(true),
        ]);

        (new AutocompleteBundle())->build($container);
        (new AutocompleteExtension())->load(['autocomplete' => []], $container);
        $container->compile();

        /** @var \MakinaCorpus\Autocomplete\AutocompleteSourceInterface $source */
        $source = $container->get('mock_source');
        $this->assertSame('<item id="17">Tabouret</item>', $source->renderItemMarkup(new MockItem('17', "Tabouret")));
    }
}
