<?php

namespace MakinaCorpus\AutocompleteBundle\Autocomplete;

use Symfony\Component\Templating\EngineInterface;

/**
 * You may use this implementation if you need a simple template to render
 * the item markup; just override the getTemplateName() name to return a valid
 * template identifier the framework template locator will understand.
 *
 * Per default, the only variables that will be set in this template will be
 * the 'value' variable, which contains the business object you loaded in one
 * of the find*() methods.
 *
 * You don't need to inject the templating engine yourself, a compiler pass
 * will do it.
 */
trait TemplateAutocompleteSourceTrait /* implements AutocompleteSourceInterface */
{
    use AutocompleteSourceTrait;

    private $templateEngine;
    private $templateName;

    /**
     * Set twig environment instance
     *
     * @param EngineInterface $templateEngine
     */
    final public function setTemplateEngine(EngineInterface $templateEngine)
    {
        $this->templateEngine = $templateEngine;
    }

    /**
     * Set template name
     *
     * @param string $name
     */
    final public function setTemplateName($name)
    {
        $this->templateName = $name;
    }

    /**
     * Get the template name.
     *
     * This method may be overloaded by the implementor.
     *
     * @return string
     */
    protected function getTemplateName()
    {
        if (!$this->templateName) {
            throw new \LogicException("You must set a template name");
        }

        return $this->templateName;
    }

    /**
     * {@inheritdoc}
     */
    public function renderItemMarkup($value)
    {
        return $this->templateEngine->render($this->getTemplateName(), ['value' => $value]);
    }
}
