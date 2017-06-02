<?php

namespace MakinaCorpus\AutocompleteBundle\Autocomplete;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Autocomplete source for the TextAutocomplete widget
 */
class AutocompleteSourceRegistry
{
    use ContainerAwareTrait;

    private $map = [];
    private $hashes = [];

    /**
     * Default constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
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
        return md5(get_class($source));
    }

    /**
     * Can be a class or an container service identifier
     *
     * @param string $class
     * @param null $managerName
     *
     * @return AutocompleteSourceInterface
     */
    public function getSource($class, $managerName = null)
    {
        $manager = $this->container->get('doctrine')->getManager($managerName);
        // $metadata = $manager->getClassMetadata($class);
        /** @var AutocompleteSourceInterface $repository */
        $repository = $manager->getRepository($class);
        if(!(method_exists($repository,'autocomplete'))){
            throw new \InvalidArgumentException(sprintf("Repository of entity %s not implement AutocompleteSourceInterface", $class));
        }
        return $repository;
    }
}
