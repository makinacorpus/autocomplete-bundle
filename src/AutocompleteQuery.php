<?php

declare(strict_types=1);

namespace MakinaCorpus\Autocomplete;

final class AutocompleteQuery
{
    const DEFAULT_LIMIT = 30;

    private int $limit = self::DEFAULT_LIMIT;
    private int $page = 1;
    private string $text;

    public function __construct(string $text, int $limit, int $page)
    {
        $this->limit = $limit;
        $this->page = $page === 0 ? 1 : $page;
        $this->text = $text;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->limit;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getSearchString(): string
    {
        return $this->text;
    }
}
