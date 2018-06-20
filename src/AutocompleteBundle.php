<?php

namespace MakinaCorpus\AutocompleteBundle;

use MakinaCorpus\AutocompleteBundle\DependencyInjection\Compiler\RegisterAutocompleteSourcePass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AutocompleteBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterAutocompleteSourcePass());
    }
}
