# Autocomplete widget and form type for Symfony

**This is a very early release**, this package is far from being complete
and provide minimal functionnalities as of today.

# Installation

```sh
composer require makinacorpus/autocomplete-bundle
```

**This autocomplete widget works with the [select2](https://select2.github.io)**
**library, it is your responsability to ensure the javascript is correctly loaded**
**for it to work.**

**You also need jQuery installed** (sorry, this might change later).

Register the routing.yml file in your ``app/routing.yml`` file:

```yaml
autocomplete:
    resource: "@AutocompleteBundle/Resources/config/routing.yml"
    prefix: /
```

And the associated form theme in your ``app/config.yml`` file:
```yaml
twig:
    debug:            "%kernel.debug%"
    strict_variables: false
    form_themes:
        # ...
        - "AutocompleteBundle:Form:fields.html.twig"
```

And it should probably work.

# Usage

First, implement an ``MakinaCorpus\AutocompleteBundle\Autocomplete\AutocompleteSourceInterface``
class, that is meant to reflect the business objects in which to autocomplete.

Then just use the ``MakinaCorpus\AutocompleteBundle\Form\Type\TextAutocompleteType`` form type
in your own form builders, and set the source class name as the ``source`` parameter for the form type.
