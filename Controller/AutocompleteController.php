<?php

namespace MakinaCorpus\AutocompleteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class AutocompleteController extends Controller
{
    /**
     * Autocomplete action
     */
    public function findAction($type, $string)
    {
        /** @var \MakinaCorpus\AutocompleteBundle\Autocomplete\AutocompleteSourceRegistry $registry */
        $registry = $this->get('autocomplete.source_registry');
        $source = $registry->getSource($type);

        $ret = [];
        foreach ($source->find($string) as $value) {
            $id = $source->transform($value);
            $ret[$id] = $id;
        }

        return new JsonResponse($ret);
    }
}
