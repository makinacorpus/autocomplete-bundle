<?php

declare (strict_types=1);

namespace MakinaCorpus\Autocomplete\Bundle\Form\Type;

use MakinaCorpus\Autocomplete\Bundle\DependencyInjection\SourceRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Data input for this widget is a string, it can be either:
 *
 *   - an object identifier, as string,
 *   - a complete label ("id - label").
 *
 * Return will be:
 *
 *   - if validation is enabled, always only the id,
 *   - if validation is disabled, but entry is valid, only the id,
 *   - if validation is disabled, but entry is invalid, the whol user input.
 */
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
            // Set this to allow arbitrary values that user might have pasted
            // in the input box to be used as data. If set to false, the widget
            // will not allow to be used without selecting a value from the
            // autocomplete.
            'allow_arbitrary_input' => false,
            // Set this in conjuction with 'allow_arbitrary_input' to allow
            // invalid values from being submitted. May be useful for forms such
            // as search filters, where validation is not mandatory.
            'allow_validation_bypass' => false,
            'required' => false,
            // If set to true, display input will contain "ID - Label", if set
            // false, display input will contain "Label", if set to null, value
            // ID will only be displayed when 'allow_arbitrary_input' is set to
            // true.
            'show_id_in_label' => null,
            // This is the type the autocomplete source you wish to use was
            // registered with. Per default, it's the AutocompleteSourceInterface
            // implementation class name.
            'type' => null, 
        ]);

        $resolver->setAllowedTypes('allow_arbitrary_input', ['bool']);
        $resolver->setAllowedTypes('allow_validation_bypass', ['bool']);
        $resolver->setAllowedTypes('type', ['string']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (empty($options['type'])) {
            throw new InvalidArgumentException("'type' option is required.");
        }

        $allowArbitraryInput = $options['allow_arbitrary_input'];
        $allowValidationBypass = $options['allow_validation_bypass'];
        $showIdInLabel = $options['show_id_in_label'] ?? $allowArbitraryInput;

        $source = $this->sourceRegistry->getSource($options['type']);
        $route = $this->urlGenerator->generate('makinacorpus_autocomplete_find', ['type' => \md5($options['type'])]);
        $uniqueId = \uniqid('ac-', true);

        $builder->add('label', TextType::class, [
            'label' => false,
            'required' => $options['required'],
            'attr' => [
                'data-tac-id' => $uniqueId,
                'data-tag-show-id' => $showIdInLabel ? "true" : "",
                'data-tac-role' => 'label',
                'data-tac-uri' => $route,
            ] + ($options['attr'] ?? []),
        ]);
        $builder->add('id', HiddenType::class, [
            'attr' => [
                'data-tac-role' => 'id',
                'data-tac-id' => $uniqueId,
            ],
        ]);

        $builder->addModelTransformer(new AutocompleteDataTransformer($source, $allowArbitraryInput, $allowValidationBypass));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (empty($view->vars['attr']['class'])) {
            $view->vars['attr']['class'] = 'tac-container';
        } else {
            $view->vars['attr']['class'] .= ' tac-container';
        }
    }
}
