# Autocomplete widget and form type for Symfony

**This is a very early release**, this package is far from being complete
and provide minimal functionnalities as of today.

# Installation

```sh
composer require makinacorpus/autocomplete-bundle
```

For it work, the JavaScript file to use may be find in the Drupal module, that
you should copy manually in your local assets: ``misc/autocomplete.js``

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
