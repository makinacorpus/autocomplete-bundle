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
        ));

        $resolver->setAllowedTypes('source', ['string']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $source = $this->sourceRegistry->getSource($options['source']);
        $options['source_instance'] = $source;

        $builder->addModelTransformer(new TextAutocompleteDataTransformer($source));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $source = $this->sourceRegistry->getSource($options['source']);

        $view->vars['route'] = $this->router->generate('mc_autocomplete', ['type' => $this->sourceRegistry->toString($source)]);
        $view->vars['multiple'] = (bool)$options['multiple'];
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextType::class;
    }
}
