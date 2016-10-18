<?php

namespace MakinaCorpus\AutocompleteBundle\DependencyInjection\Compiler;

use MakinaCorpus\AutocompleteBundle\Autocomplete\AutocompleteSourceInterface;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class RegisterAutocompleteSourcePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $registryName = 'autocomplete.source_registry';
        $tagName = 'autocomplete.source';

        if (!$container->hasDefinition($registryName) && !$container->hasAlias($registryName)) {
            return;
        }
        $definition = $container->findDefinition($registryName);

        $map = [];
        foreach ($container->findTaggedServiceIds($tagName) as $id => $attributes) {

            $def = $container->getDefinition($id);

            if (!$def->isPublic()) {
                throw new \InvalidArgumentException(sprintf('The service "%s" must be public as sources are lazy-loaded.', $id));
            }
            if ($def->isAbstract()) {
                throw new \InvalidArgumentException(sprintf('The service "%s" must not be abstract.', $id));
            }

            // We must assume that the class value has been correctly filled, even if the service is created by a factory
            //   - note from myself: this is documented that it should alway be
            //     in the official dependency injection documentation
            $class = $container->getParameterBag()->resolveValue($def->getClass());

            $refClass = new \ReflectionClass($class);
            if (!$refClass->implementsInterface(AutocompleteSourceInterface::class)) {
                throw new \InvalidArgumentException(sprintf('Service "%s" must implement "%s".', $id, AutocompleteSourceInterface::class));
            }

            $map[$class] = $id;
        }

        if ($map) {
            $definition->addMethodCall('registerAll', [$map]);
        }
    }
}
