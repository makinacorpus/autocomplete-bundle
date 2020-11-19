<?php

declare(strict_types=1);

namespace MakinaCorpus\Autocomplete\Bundle;

use MakinaCorpus\Autocomplete\Bundle\DependencyInjection\Compiler\RegisterAutocompleteSourcePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AutocompleteBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterAutocompleteSourcePass());
    }
}
