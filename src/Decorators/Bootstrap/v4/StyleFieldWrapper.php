<?php

namespace Nicat\FormFactory\Decorators\Bootstrap\v4;

use Nicat\FormFactory\Components\Additional\FieldWrapper;
use Nicat\FormFactory\Components\FormControls\CheckboxInput;
use Nicat\FormFactory\Components\FormControls\RadioInput;
use Nicat\HtmlFactory\Decorators\Abstracts\Decorator;

class StyleFieldWrapper extends Decorator
{

    /**
     * The element to be decorated.
     *
     * @var FieldWrapper
     */
    protected $element;

    /**
     * Returns the group-ID of this decorator.
     *
     * Returning null means this decorator will always be applied.
     *
     * @return string|null
     */
    public static function getGroupId()
    {
        return 'bootstrap:v4';
    }

    /**
     * Returns an array of class-names of elements, that should be decorated by this decorator.
     *
     * @return string[]
     */
    public static function getSupportedElements(): array
    {
        return [
            FieldWrapper::class
        ];
    }

    /**
     * Perform decorations on $this->element.
     */
    public function decorate()
    {
        $this->element->addClass($this->getFieldWrapperClass());
		$this->element->addClass($this->getFieldWrapperInlineClass());

        if (!is_null($this->element->field)) {

            // Add error-class to wrapper, if field has errors.
            if ($this->element->field->hasErrors()) {
                $this->element->addClass('has-error');
            }
        }
    }

    /**
     * Returns the correct class for the field's wrapper.
     *
     * @return string
     */
    private function getFieldWrapperClass()
    {
        if (!is_null($this->element->field) && ($this->element->field->is(CheckboxInput::class) || $this->element->field->is(RadioInput::class))) {
            return 'form-check';
        }

        return 'form-group';
    }

    /**
     * Returns the correct class for the field's wrapper, if the field should be displayed inline.
     *
     * @return string
     */
    private function getFieldWrapperInlineClass()
    {
        if (!is_null($this->element->field) && ($this->element->field->is(CheckboxInput::class) || $this->element->field->is(RadioInput::class)) && $this->element->field->isInline()) {
            return 'form-check-inline';
        }
		
		return '';
    }
}