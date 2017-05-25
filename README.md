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
class to custom entity repository, that is meant to reflect the business objects in which to autocomplete.

```php
<?php
# src/AppBundle/Entity/User.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User 
...
```

```php
<?php
# src/AppBundle/Entity/User.php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use MakinaCorpus\AutocompleteBundle\Autocomplete\AutocompleteSourceInterface;

class UserRepository extends EntityRepository implements AutocompleteSourceInterface
{
    public function autocomplete($string, $limit = 30, $offset = 0)
    {
        return $this->createQueryBuilder('entity')
            // PostgreSQL function lower need to ignore case
            ->where('lower(entity.name) LIKE :Query')
            ->setParameter('Query','%'.mb_strtolower($string).'%')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    
    public function findAllById($idList)
    {
        return $this->createQueryBuilder('entity')
            ->where('entity.id IN (:List)')
            ->setParameter('List', $idList)
            ->getQuery()
            ->getResult();
    }
    
    public function getId($value)
    {
        // Get id from finded entity
        return $value->getId();
    }

    public function getLabel($value)
    {
        // Get title from finded entity
        return $value->getName();
    }

    public function getExtraData($value)
    {
        return [];
    }

    // Default markup: Set bold to finded part
    public function getMarkup($value, $string = '')
    {
        $str = $this->getLabel($value);
        return $string === '' ? $str : preg_replace('/('.$string.')/ui', '<b>$1</b>', $str);
    }

}
```


Then just use the ``MakinaCorpus\AutocompleteBundle\Form\Type\TextAutocompleteType`` form type
in your own form builders, and set the source class name as the ``source`` parameter for the form type.

And include to your default form template 

```twig
<script src="{{ asset('bundles/autocomplete/js/autocomplete.js') }}"></script>
```

# Customization

## Select2

For customize filds you may replace include JS whith your own select2 initialization:

```html
    <script>
        $(function () {
            $('.mc-autocomplete').each(function () {
                var $this = $(this);
                $this.select2({
                    // Language
                    language: "ru",
                    // Theme https://github.com/select2/select2-bootstrap-theme
                    theme: "bootstrap",
                    placeholder: "Пожалуйста выберите...",
                    ajax: {
                        url: $this.data('url'),
                        allowClear: true,
                        dataType: 'json',
                        delay: 500,
                        data: function (params) {
                            return {
                                query: params.term, // search term
                                page: params.page,
                                limit: 30
                            };
                        },
                        processResults: function (data, params) {
                            // parse the results into the format expected by Select2
                            // since we are using custom formatting functions we do not need to
                            // alter the remote JSON data, except to indicate that infinite
                            // scrolling can be used
                            params.page = params.page || 1;
                            return {
                                results: data.items,
                                pagination: {
                                    more: (params.page * 30) < data.total
                                }
                            };
                        },
                        cache: true
                    },
                    // We already did that in the PHP controller
                    escapeMarkup: function (markup) { return markup; },
                    minimumInputLength: 1,
                    // No need for this, rendered in PHP side
                    // templateResult: function () {},
                    templateSelection: function (data) { return data.title || data.text;}
                });
            });
        });
    </script>
```
