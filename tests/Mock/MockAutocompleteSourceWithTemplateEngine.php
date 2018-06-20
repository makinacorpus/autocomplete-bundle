<?php

namespace MakinaCorpus\Autocomplete\Tests\Mock;

use MakinaCorpus\Autocomplete\Bundle\TemplateAutocompleteSourceTrait;

class MockAutocompleteSourceWithTemplateEngine extends MockAutocompleteSource
{
    use TemplateAutocompleteSourceTrait;

    public function __construct()
    {
        $this->setTemplateName('mock_item_template');
    }
}
