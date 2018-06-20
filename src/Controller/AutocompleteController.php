<?php

namespace MakinaCorpus\AutocompleteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AutocompleteController extends Controller
{
    /**
     * Autocomplete action
     */
    public function findAction(Request $request, $type)
    {
        /** @var \MakinaCorpus\AutocompleteBundle\Autocomplete\AutocompleteSourceRegistry $registry */
        $registry = $this->get('autocomplete.source_registry');
        $source = $registry->getSource($type);

        $query  = $request->query->get('query');
        $limit  = $request->query->get('limit', 30);
        $page   = $request->query->get('page', 1);
        $offset = max([($page - 1), 0]) * $limit;

        if (empty($query)) { // empty() trims empty strings
            throw $this->createNotFoundException();
        }

        $items = [];
        foreach ($source->find($query, $limit, $offset) as $value) {
            $items[] = [
                'id'    => $source->getItemId($value),
                'title' => $source->getItemLabel($value),
                'text'  => $source->renderItemMarkup($value),
            ] + $source->getItemExtraData($value);
        }

        return new JsonResponse([
            'limit' => (int)$limit,
            'page'  => (int)$page,
            'total' => count($items),
            'items' => $items,
        ]);
    }
}
