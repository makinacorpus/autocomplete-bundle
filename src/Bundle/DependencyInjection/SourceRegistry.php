<?php

declare(strict_types=1);

namespace MakinaCorpus\Autocomplete\Bundle\DependencyInjection;

use MakinaCorpus\Autocomplete\AutocompleteSourceInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class SourceRegistry
{
    use ContainerAwareTrait;

    private array $map = [];
    private array $hashes = [];

    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    /**
     * Register all sources.
     *
     * @param string[] $map
     *   Keys are item class (or type) values are service identifiers.
     *   Each AutocompleteSourceInterface implementation is a service.
     */
    public function registerAll(array $map)
    {
        $this->map = $map;
        foreach ($this->map as $class => $service) {
            $this->hashes[\md5($class)] = $service;
        }
    }

    /**
     * Can be a class or an container service identifier.
     */
    public function getSource(string $class): AutocompleteSourceInterface
    {
        // Attempt to find item by class.
        if (isset($this->map[$class])) {
            return $this->container->get($this->map[$class]);
        }

        // Fallback on potential container service.
        $pos = \array_search($class, $this->map);
        if (false !== $pos) {
            return $this->container->get($class);
        }

        // It might be a hash.
        if (isset($this->hashes[$class])) {
            return $this->container->get($this->hashes[$class]);
        }

        throw new \InvalidArgumentException(\sprintf("Cannot find class or service: %s", $class));
    }
}
