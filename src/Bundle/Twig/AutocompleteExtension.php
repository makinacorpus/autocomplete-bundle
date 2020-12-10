<?php

declare(strict_types=1);

namespace MakinaCorpus\Autocomplete\Bundle\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

final class AutocompleteExtension extends AbstractExtension
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('autocomplete_url', [$this, 'renderAutocompleteUrl'], ['is_safe' => ['html']]),
        ];
    }

    public function renderAutocompleteUrl(string $type): string
    {
        return $this->urlGenerator->generate('makinacorpus_autocomplete_find', ['type' => \md5($type)]);
    }
}
