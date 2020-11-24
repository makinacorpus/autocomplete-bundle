# Autocomplete API and Symfony widget

This package provides:

 * an almost dependency-free autocomplete source API, with a generic controller
   that should be suitable for most frameworks,

 * a Symfony bundle that brings a form widget, source registration, and the
   associated controller for autocompleting things into forms.

# Upgrading to 2.x

You have two fixes to make:

 - First one is that the `AutocompleteSourceInterface::find()` method query
   argument typing changed, you must adapt your sources.

 - Second is that we now support more libraries than only `select2`, so instead
   of including `AutocompleteBundle:Form:fields.html.twig` you must chose
   either one of `fields-select2.html.twig` or `fields-autocompleter.html.twig`.

 - Template engine based rendering has been dropped, it's up to you to
   implement it properly in your sources.

# Installation

```sh
composer require makinacorpus/autocomplete-bundle
```

# Write JavaScript code

You must implement the JavaScript part yourself, all you have to know is that
you have two different inputs:

 - a container with the `.tac-container` CSS class, for being more precise in
   selecting the component,
 - a `text` type input,
 - a `hidden` type input.
 - both will have the `data-tac-id` attribute (which yield the same value for
   both inputs) and the `data-tac-role` attribute, which respectively yield the
   `label` and `id` values.

The `text` input also yield the `data-autocomplete-uri` which contains a
generated URL for querying data (please note that URL can change in time, for
security or deduplication reasons). In order to query this URL you can use the
following query parameters:

 - `query` which holds the user input,
 - `limit` which holds the number of items to fetch,
 - `page` page number (which starts at `1`).

Once you get results, and the user selects a value, you must set the return
object `id` property into the `[data-tac-role="id"]` element, and the object
`label` property into the `[data-tac-role="label"]` element.

Bonus point if you empty the hidden value when the users manually edit the
text value.

## Use a supported JavaScript library

**This autocomplete widget needs a third party library to be registered**
**globally in your JavaScript code**:

 - [select2](https://select2.github.io) library,
 - [autocompleter](https://github.com/kraaden/autocomplete) library
   (not implemented yet).

If you use `select2` you also need `jQuery` to be installed (any version).

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
        # For select2 based widget:
        - "AutocompleteBundle:Form:fields-select2.html.twig"
```

**Beware that this method is not supported anymore, it might work, it might not.**

# Usage

First, implement an ``MakinaCorpus\AutocompleteBundle\Autocomplete\AutocompleteSourceInterface``
class, that is meant to reflect the business objects in which to autocomplete.

Then just use the ``MakinaCorpus\AutocompleteBundle\Form\Type\TextAutocompleteType`` form type
in your own form builders, and set the source class name as the ``source`` parameter for the form type.
