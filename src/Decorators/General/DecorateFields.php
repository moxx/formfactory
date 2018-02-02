<?php

namespace Nicat\FormBuilder\Decorators\General;

use Nicat\FormBuilder\Components\FieldWrapper;
use Nicat\FormBuilder\RulesProcessor\RulesProcessor;
use Nicat\FormBuilder\ValueProcessor\ValueProcessor;
use Nicat\FormBuilder\Elements\CheckboxInputElement;
use Nicat\FormBuilder\Elements\ColorInputElement;
use Nicat\FormBuilder\Elements\DateInputElement;
use Nicat\FormBuilder\Elements\DatetimeInputElement;
use Nicat\FormBuilder\Elements\DatetimeLocalInputElement;
use Nicat\FormBuilder\Elements\EmailInputElement;
use Nicat\FormBuilder\Elements\FileInputElement;
use Nicat\FormBuilder\Elements\HiddenInputElement;
use Nicat\FormBuilder\Elements\NumberInputElement;
use Nicat\FormBuilder\Elements\OptionElement;
use Nicat\FormBuilder\Elements\RadioInputElement;
use Nicat\FormBuilder\Elements\SelectElement;
use Nicat\FormBuilder\Elements\TextareaElement;
use Nicat\FormBuilder\Elements\TextInputElement;
use Nicat\FormBuilder\Elements\Traits\CanHaveHelpText;
use Nicat\FormBuilder\Elements\Traits\CanHaveLabel;
use Nicat\FormBuilder\Elements\Traits\UsesAutoTranslation;
use Nicat\FormBuilder\FormBuilderTools;
use Nicat\HtmlBuilder\Decorators\Abstracts\Decorator;
use Nicat\HtmlBuilder\Elements\Abstracts\Element;
use Nicat\HtmlBuilder\Elements\Traits\AllowsPlaceholderAttribute;

/**
 * Apply various decorations to FormBuilder-fields.
 *
 * Class DecorateFields
 * @package Nicat\FormBuilder\Decorators\General
 */
class DecorateFields extends Decorator
{

    /**
     * The element to be decorated.
     *
     * @var Element|CanHaveLabel|UsesAutoTranslation|AllowsPlaceholderAttribute|CanHaveHelpText
     */
    protected $element;

    /**
     * Returns an array of frontend-framework-ids, this decorator is specific for.
     * Returning an empty array means all frameworks are supported.
     *
     * @return string[]
     */
    public static function getSupportedFrameworks(): array
    {
        return [];
    }

    /**
     * Returns an array of class-names of elements, that should be decorated by this decorator.
     *
     * @return string[]
     */
    public static function getSupportedElements(): array
    {
        return [
            TextInputElement::class,
            NumberInputElement::class,
            ColorInputElement::class,
            DateInputElement::class,
            DatetimeInputElement::class,
            DatetimeLocalInputElement::class,
            EmailInputElement::class,
            HiddenInputElement::class,
            CheckboxInputElement::class,
            FileInputElement::class,
            RadioInputElement::class,
            TextareaElement::class,
            SelectElement::class
        ];
    }

    /**
     * Perform decorations on $this->element.
     */
    public function decorate()
    {
        // Automatically generate a meaningful id for fields without a manually set id.
        $this->autoGenerateID();

        //Wrap fields with the FieldWrapper.
        $this->applyFieldWrapper();

        // Apply laravel-rules to the field's attributes for browser-live-validation.
        $this->applyRules();

        // Applies default- or submitted-values to the field.
        $this->applyValues();

        // Automatically generate the label-text for fields without a manually set label using auto-translation.
        $this->autoGenerateLabelText();

        // Add an indication to the label of required form fields.
        $this->indicateRequiredFields();

        // Automatically generate the placeholder-text for fields without a manually set placeholder using auto-translation.
        $this->autoGeneratePlaceholder();

        // Automatically generate help-texts for fields without a manually set help-text using auto-translation.
        $this->autoGenerateHelpText();

    }

    /**
     * Automatically generates a meaningful id for fields without a manually set id.
     */
    protected function autoGenerateID()
    {
        // If the element already has an id, we leave it be.
        if ($this->element->attributes->isSet('id')) {
            return;
        }

        // The field-element containing the field-name is always $this->element,
        // except with option-elements, where we use the currently open select-element.
        $fieldElement = $this->element;
        if ($this->element->is(OptionElement::class)) {
            $fieldElement = $this->formBuilder->openSelect;
        }

        // If $fieldElement has no 'name' attribute set, we abort,
        // because without a name we can not auto-create an id.
        if (!$fieldElement->attributes->isSet('name')) {
            return;
        }

        // Auto-generated IDs always start with formID...
        $fieldId = $this->formBuilder->openForm->attributes->id;

        // ...followed by the field-name.
        $fieldId .= '_' . $fieldElement->attributes->name;

        // For radio-buttons and options we also append the value.
        if ($this->element->is(RadioInputElement::class) || $this->element->is(OptionElement::class)) {
            $fieldId .= '_' . $this->element->attributes->value;
        }

        $this->element->id($fieldId);
    }

    /**
     * Wrap fields with the FieldWrapper.
     */
    protected function applyFieldWrapper()
    {
        // For hidden-input-fields, a FieldWrapper does not make sense.
        if ($this->element->is(HiddenInputElement::class)) {
            return;
        }

        if (is_null($this->element->wrapper)) {
            $this->element->wrap(
                new FieldWrapper($this->element)
            );
        }
    }

    /**
     * Applies laravel-rules to the field's attributes for browser-live-validation using the RulesProcessor.
     */
    private function applyRules()
    {
        if (method_exists($this->element,'rules')) {
            new RulesProcessor($this->element);
        }
    }

    /**
     * Applies default- or submitted-values to the field using the ValueProcessor.
     */
    private function applyValues()
    {
        // OptionElements are handled via their SelectElement.
        if ($this->element->is(OptionElement::class)) {
            return;
        }

        new ValueProcessor($this->element);
    }

    /**
     * Automatically generates the label-text for fields without a manually set label using auto-translation.
     */
    protected function autoGenerateLabelText()
    {
        if (method_exists($this->element,'label') && is_null($this->element->label)) {
            $defaultValue = ucwords(FormBuilderTools::arrayStripString($this->element->attributes->name));
            if ($this->element->is(RadioInputElement::class)) {
                $defaultValue = ucwords($this->element->attributes->value);
            }
            $this->element->label(
                $this->element->performAutoTranslation($defaultValue)
            );
        }
    }

    /**
     * Adds an indication to the label of required form fields.
     */
    protected function indicateRequiredFields()
    {
        // TODO: Make decoratable.
        if (!$this->element->is(RadioInputElement::class) && method_exists($this->element,'label')) {
            if (!is_null($this->element->label) && $this->element->attributes->isSet('required')) {
                $this->element->label(
                    $this->element->label . '<sup>*</sup>'
                );
            }
        }
    }

    /**
     * Automatically generates the placeholder-text for fields without a manually set placeholder using auto-translation.
     */
    private function autoGeneratePlaceholder()
    {
        if ($this->element->attributes->isAllowed('placeholder') && !$this->element->attributes->isSet('placeholder')) {
            $defaultValue = ucwords(FormBuilderTools::arrayStripString($this->element->attributes->name));
            $this->element->placeholder(
                $this->element->performAutoTranslation($defaultValue,'Placeholder')
            );
        }
    }

    /**
     * Automatically generates help-texts for fields without a manually set help-text using auto-translation.
     */
    protected function autoGenerateHelpText()
    {
        if (method_exists($this->element,'hasHelpText') && !$this->element->hasHelpText()) {
            $helpText = $this->element->performAutoTranslation(null, 'HelpText');
            if ($helpText !== null) {
                $this->element->helpText($helpText);
            }
        }
    }

}