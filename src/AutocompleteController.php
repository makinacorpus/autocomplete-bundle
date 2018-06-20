<?php

namespace MakinaCorpus\Autocomplete;

use MakinaCorpus\Calista\Query\InputDefinition;
use MakinaCorpus\Calista\Query\Query;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Default controller implementation
 */
class AutocompleteController
{
    private $limitParameter = 'limit';
    private $pageParameter = 'page';
    private $searchParameter = 'query';

    /**
     * Default constructor
     */
    public function __construct(string $searchParameter = 'query', $limitParameter = 'limit', $pageParameter = 'page')
    {
        $this->limitParameter = $limitParameter;
        $this->pageParameter = $pageParameter;
        $this->searchParameter = $searchParameter;
    }

    /**
     * Create query from request
     */
    private function createQuery(Request $request): Query
    {
        return (new InputDefinition([
            'limit_allowed' => true,
            'limit_default' => 100,
            'limit_param' => $this->limitParameter,
            'pager_enable' => true,
            'pager_param' => $this->pageParameter,
            'search_enable' => true,
            'search_param' => $this->searchParameter,
        ]))->createQueryFromRequest($request);
    }

    public function findArray(Request $request, AutocompleteSourceInterface $source): array
    {
        $query = $this->createQuery($request);

        if (!$query->getRawSearchString()) {
            throw new NotFoundHttpException();
        }

        return $source->find($query);
    }

    public function findJson(Request $request, AutocompleteSourceInterface $source): Response
    {
        $query = $this->createQuery($request);

        if (!$query->getRawSearchString()) {
            throw new NotFoundHttpException();
        }

        $items = [];
        foreach ($source->find($query) as $value) {
            $items[] = [
                'id'    => $source->getItemId($value),
                'title' => $source->getItemLabel($value),
                'text'  => $source->renderItemMarkup($value),
            ] + $source->getItemExtraData($value);
        }

        return new JsonResponse([
            'limit' => (int)$query->getLimit(),
            'page'  => (int)$query->getPageNumber(),
            'total' => count($items),
            'items' => $items,
        ]);
    }
}
