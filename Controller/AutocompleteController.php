<?php

namespace MakinaCorpus\AutocompleteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AutocompleteController extends Controller
{
    /**
     * Autocomplete action
     * @param Request $request
     * @param $type
     * @return JsonResponse
     */
    public function findAction(Request $request, $type)
    {
        /** @var \MakinaCorpus\AutocompleteBundle\Autocomplete\AutocompleteSourceRegistry $registry */
        $registry = $this->get('autocomplete.source_registry');
        $str = base64_decode($type);
        $str = preg_replace('/[^[:alnum:]:\\\\\/\-_]/', '', $str);
        if (empty($str)) {
            return new JsonResponse();
        }
        try {
            $source = $registry->getSource($str);
        } catch (\Exception $exception) {
            return new JsonResponse(['error' => $exception->getMessage()]);
        }

        $query = $request->query->get('query');
        $limit = $request->query->get('limit', 30);
        $page = $request->query->get('page', 1);
        $offset = max([($page - 1), 0]) * $limit;

        if (empty($query)) { // empty() trims empty strings
            throw $this->createNotFoundException();
        }

        $items = [];
        foreach ($source->autocomplete($query, $limit, $offset) as $value) {
            $items[] = [
                    'id' => $source->getId($value),
                    'title' => $source->getLabel($value),
                    'text' => $source->getMarkup($value, $query),
                ] + $source->getExtraData($value);
        }

        return new JsonResponse([
            'limit' => (int)$limit,
            'page' => (int)$page,
            'total' => count($items),
            'items' => $items,
        ]);
    }
}
