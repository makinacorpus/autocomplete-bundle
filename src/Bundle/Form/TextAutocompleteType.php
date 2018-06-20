<?php

namespace MakinaCorpus\AutocompleteBundle\Form\Type;

use MakinaCorpus\Autocomplete\Bundle\DependencyInjection\SourceRegistry;
use MakinaCorpus\Autocomplete\Bundle\Form\Type\TextAutocompleteDataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextAutocompleteType extends AbstractType
{
    private $sourceRegistry;

    /**
     * Default constructor
     */
    public function __construct(SourceRegistry $sourceRegistry)
    {
        $this->sourceRegistry = $sourceRegistry;
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

        $view->vars['route'] = $this->sourceRegistry->getUrl($source);
        $view->vars['multiple'] = (bool)$options['multiple'];
        $value = $form->getData();
        if ($value) {
            $view->vars['value_id'] = $source->getItemId($value);
            $view->vars['value_label'] = $source->getItemLabel($value);
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
