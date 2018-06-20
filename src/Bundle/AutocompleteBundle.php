<?php

namespace MakinaCorpus\Autocomplete\Bundle;

use MakinaCorpus\Autocomplete\Bundle\DependencyInjection\Compiler\RegisterAutocompleteSourceCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AutocompleteBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterAutocompleteSourceCompilerPass());
    }
}
