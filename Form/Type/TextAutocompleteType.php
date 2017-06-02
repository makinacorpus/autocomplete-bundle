<?php

namespace MakinaCorpus\AutocompleteBundle\Form\Type;

use MakinaCorpus\AutocompleteBundle\Autocomplete\AutocompleteSourceRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class TextAutocompleteType extends AbstractType
{
    private $sourceRegistry;
    private $router;

    /**
     * Default constructor
     *
     * @param AutocompleteSourceRegistry $sourceRegistry
     * @param RouterInterface $router
     */
    public function __construct(AutocompleteSourceRegistry $sourceRegistry, RouterInterface $router)
    {
        $this->sourceRegistry = $sourceRegistry;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'compound' => false,
            'source' => null,
            'multiple' => false,
            'doctrine_manager' => null, // Default entity manager
            'tags' => false,
        ));

        $resolver->setAllowedTypes('source', ['string']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $source = $this->sourceRegistry->getSource($options['source'], $options['doctrine_manager']);
        $options['source_instance'] = $source;

        $builder->addModelTransformer(new TextAutocompleteDataTransformer($source, $options));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $source = $this->sourceRegistry->getSource($options['source'], $options['doctrine_manager']);

        $view->vars['route'] = $this->router->generate('mc_autocomplete', ['type' => base64_encode($options['source'])]);
        $view->vars['multiple'] = (bool)$options['multiple'];
        $view->vars['tags'] = (bool)$options['tags'];
        $value = $form->getData();
        if ($value) {
            $view->vars['value_id'] = $source->getId($value);
            $view->vars['value_label'] = $source->getLabel($value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextType::class;
    }
}
