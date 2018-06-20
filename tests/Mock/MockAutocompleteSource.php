<?php

namespace MakinaCorpus\Autocomplete\Tests\Mock;

use MakinaCorpus\Autocomplete\AutocompleteSourceInterface;
use MakinaCorpus\Autocomplete\AutocompleteSourceTrait;
use MakinaCorpus\Calista\Query\Query;

class MockAutocompleteSource implements AutocompleteSourceInterface
{
    use AutocompleteSourceTrait;

    private $generate = true;

    public function toggleGeneration(bool $toggle)
    {
        $this->generate = $toggle;
    }

    public function find(Query $query): array
    {
        if ($query->getRawSearchString() === 'some') {
            return \array_slice(
                \array_map(
                    function ($index) {
                        return new MockItem($index, "some".$index);
                    },
                    \range(1, 100)
                ),
                $query->getOffset(),
                $query->getLimit()
            );
        }
        return [];
    }

    public function findAllById(array $idList): array
    {
        if ($this->generate) {
            return \array_map(function ($id) {
                return new MockItem($id, "Label of $id");
            }, $idList);
        }
        return [];
    }

    public function getItemId($value): string
    {
        if (!$value instanceof MockItem) {
            throw new \InvalidArgumentException();
        }

        return (string)$value->id;
    }
}
