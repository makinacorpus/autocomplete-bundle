<?php

namespace MakinaCorpus\Autocomplete\Bundle\Controller;

use MakinaCorpus\Autocomplete\AutocompleteController as BaseController;
use MakinaCorpus\Autocomplete\Bundle\DependencyInjection\SourceRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AutocompleteController extends Controller
{
    public function find(SourceRegistry $registry, Request $request, $type): Response
    {
        return (new BaseController('query', 'limit', 'page'))->findJson($request, $registry->getSource($type));
    }
}
