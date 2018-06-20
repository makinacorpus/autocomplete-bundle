<?php

namespace MakinaCorpus\Autocomplete\Tests\Mock;

use Symfony\Component\Templating\EngineInterface;

class MockTemplateEngine implements EngineInterface
{
    const TEMPLATE_ITEM = 'mock_item_template';

    public function exists($name)
    {
        return self::TEMPLATE_ITEM === $name;
    }

    public function supports($name)
    {
        return self::TEMPLATE_ITEM === $name;
    }

    public function render($name, array $parameters = [])
    {
        if (self::TEMPLATE_ITEM !== $name || !isset($parameters['value']) || !$parameters['value'] instanceof MockItem) {
            throw new \InvalidArgumentException();
        }

        return strtr('<item id="%id%">%label%</item>', [
            '%id%' => $parameters['value']->id,
            '%label%' => $parameters['value']->label,
        ]);
    }
}
