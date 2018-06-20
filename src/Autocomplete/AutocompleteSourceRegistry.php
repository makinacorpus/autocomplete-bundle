<?php

namespace MakinaCorpus\AutocompleteBundle\Autocomplete;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Autocomplete source for the TextAutocomplete widget
 */
class AutocompleteSourceRegistry
{
    use ContainerAwareTrait;

    private $map = [];
    private $hashes = [];
    private $urlGenerator;

    /**
     * Default constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, UrlGeneratorInterface $urlGenerator)
    {
        $this->setContainer($container);
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Register all sources
     *
     * @param string[] $map
     */
    public function registerAll($map)
    {
        $this->map = $map;
        foreach ($this->map as $class => $service) {
            $this->hashes[md5($class)] = $service;
        }
    }

    /**
     * Source to string
     */
    public function toString($source)
    {
        return md5(is_string($source) ? $source : get_class($source));
    }

    /**
     * Source to URL
     */
    public function getUrl($source)
    {
        return $this->urlGenerator->generate('mc_autocomplete', ['type' => $this->toString($source)]);
    }

    /**
     * Can be a class or an container service identifier
     *
     * @param string $class
     *
     * @return AutocompleteSourceInterface
     */
    public function getSource($class)
    {
        // Attempt to find item by class
        if (isset($this->map[$class])) {
            return $this->container->get($this->map[$class]);
        }

        // Fallback on potential container service
        $pos = array_search($class, $this->map);
        if (false !== $pos) {
            return $this->container->get($class);
        }

        // It might be a hash
        if (isset($this->hashes[$class])) {
            return $this->container->get($this->hashes[$class]);
        }

        throw new \InvalidArgumentException(sprintf("%s: cannot find class or service", $class));
    }
}
