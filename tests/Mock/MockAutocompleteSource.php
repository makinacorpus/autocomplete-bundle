<?php

declare(strict_types=1);

namespace MakinaCorpus\Autocomplete\Tests\Mock;

use MakinaCorpus\Autocomplete\AutocompleteSourceInterface;
use MakinaCorpus\Autocomplete\AutocompleteSourceTrait;
use MakinaCorpus\Autocomplete\AutocompleteQuery;

final class MockAutocompleteSource implements AutocompleteSourceInterface
{
    use AutocompleteSourceTrait;

    private bool $generate = true;

    public function toggleGeneration(bool $toggle)
    {
        $this->generate = $toggle;
    }

    public function find(AutocompleteQuery $query): array
    {
        if ($query->getSearchString() === 'some') {
            return \array_slice(
                \array_map(
                    fn($index) => new MockItem((string) $index, "some".$index),
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
            return \array_map(fn ($id) => new MockItem($id, "Label of $id"), $idList);
        }
        return [];
    }

    public function getItemId($value): string
    {
        if (!$value instanceof MockItem) {
            throw new \InvalidArgumentException();
        }

        return (string) $value->id;
    }
}
