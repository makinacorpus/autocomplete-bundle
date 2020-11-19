<?php

declare(strict_types=1);

namespace MakinaCorpus\Autocomplete\Tests\Mock;

final class MockItem
{
    public $id;
    public $label;

    public function __construct(string $id, string $label)
    {
        $this->id = $id;
        $this->label = $label;
    }
}
