<?php

namespace MakinaCorpus\Autocomplete\Tests\Mock;

class MockItem
{
    public $id;
    public $label;

    public function __construct(string $id, string $label)
    {
        $this->id = $id;
        $this->label = $label;
    }
}
