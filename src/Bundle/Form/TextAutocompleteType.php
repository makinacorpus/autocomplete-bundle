<?php

declare (strict_types=1);

namespace MakinaCorpus\Autocomplete\Bundle\Form\Type;

use MakinaCorpus\Autocomplete\Bundle\DependencyInjection\SourceRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TextAutocompleteType extends AbstractType
{
    private SourceRegistry $sourceRegistry;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(SourceRegistry $sourceRegistry, UrlGeneratorInterface $urlGenerator)
    {
        $this->sourceRegistry = $sourceRegistry;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'compound' => false,
            'source' => null,
        ]);

        // Source is the target item class name, which was used for autocomplete
        // type registration.
        $resolver->setAllowedTypes('source', ['string']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $source = $this->sourceRegistry->getSource($options['source']);
        $options['source_instance'] = $source;
        $builder->addModelTransformer(new AutocompleteDataTransformer($source));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $source = $this->sourceRegistry->getSource($options['source']);
        $view->vars['route'] = $this->urlGenerator->generate('makinacorpus_autocomplete_find', ['type' => \md5($options['source'])]);
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
