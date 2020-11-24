<?php

declare(strict_types=1);

namespace MakinaCorpus\Autocomplete;

use MakinaCorpus\Autocomplete\Bundle\DependencyInjection\SourceRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class AutocompleteController
{
    private string $limitParameter = 'limit';
    private string $pageParameter = 'page';
    private string $searchParameter = 'query';

    public function __construct(string $searchParameter = 'query', $limitParameter = 'limit', $pageParameter = 'page')
    {
        $this->limitParameter = $limitParameter;
        $this->pageParameter = $pageParameter;
        $this->searchParameter = $searchParameter;
    }

    public function find(SourceRegistry $registry, Request $request, $type): Response
    {
        return $this->findJson($request, $registry->getSource($type));
    }

    public function findJson(Request $request, AutocompleteSourceInterface $source): Response
    {
        $query = $this->createQuery($request);

        if (!$query->getSearchString()) {
            throw new NotFoundHttpException();
        }

        $items = [];
        foreach ($source->find($query) as $value) {
            $items[] = [
                'id' => $source->getItemId($value),
                'title' => $source->getItemLabel($value),
                'text' => $source->renderItemMarkup($value),
            ] + $source->getItemExtraData($value);
        }

        return new JsonResponse([
            'limit' => $query->getLimit(),
            'page'  => $query->getPage(),
            'total' => \count($items),
            'items' => $items,
        ]);
    }

    public function findArray(Request $request, AutocompleteSourceInterface $source): iterable
    {
        $query = $this->createQuery($request);

        if (!$query->getSearchString()) {
            throw new NotFoundHttpException();
        }

        return $source->find($query);
    }

    private function createQuery(Request $request): AutocompleteQuery
    {
        return new AutocompleteQuery(
            \trim((string) $request->query->get($this->searchParameter)),
            $this->validateInt((string) $request->query->get($this->limitParameter), AutocompleteQuery::DEFAULT_LIMIT),
            $this->validateInt((string) $request->query->get($this->pageParameter), 1)
        );
    }

    private function validateInt(string $parameter, int $default): int
    {
        if (!\ctype_digit($parameter)) {
            return $default;
        }
        return \abs((int) $parameter);
    }
}
