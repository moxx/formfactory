<?php

namespace Nicat\FormFactory\Components\Additional;

use Nicat\FormFactory\Components\Contracts\FieldInterface;
use Nicat\FormFactory\Components\Contracts\FormControlInterface;
use Nicat\FormFactory\Components\Contracts\LabelInterface;
use Nicat\FormFactory\Components\FormControls\RadioInput;
use Nicat\FormFactory\Utilities\FormFactoryTools;
use Nicat\HtmlFactory\Elements\Abstracts\Element;
use Nicat\HtmlFactory\Elements\LabelElement;

class FieldLabel extends LabelElement
{
    /**
     * The field this FieldLabel belongs to.
     *
     * @var Element|LabelInterface|FormControlInterface|FieldInterface
     */
    public $field;

    /**
     * The label-text.
     *
     * @var string
     */
    protected $text;

    /**
     * Should the label be displayed?
     *
     * @var bool
     */
    public $displayLabel = true;

    /**
     * Signals the view, that this label should wrap a checkable field,
     * instead of being rendered bound after the field.
     *
     * @var bool
     */
    public $wrapCheckable = false;

    /**
     * Should the label include an indicator for required fields?
     *
     * @var bool
     */
    public $displayRequiredFieldIndicator = true;

    /**
     * FieldLabel constructor.
     *
     * @param Element|LabelInterface $field
     */
    public function __construct(LabelInterface $field)
    {
        parent::__construct();
        $this->field = $field;
    }

    /**
     * Sets the label-text.
     *
     * @param $text
     * @return FieldLabel
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    public function beforeDecoration()
    {

        if ($this->displayLabel) {

            // Perform auto-translation, if no label was manually set.
            if (!$this->hasLabel()) {
                $defaultValue = ucwords(FormFactoryTools::arrayStripString($this->field->attributes->name));
                if ($this->field->is(RadioInput::class)) {
                    $defaultValue = ucwords($this->field->attributes->value);
                }
                $this->setText(
                    $this->field->performAutoTranslation($defaultValue)
                );
            }

            $this->for($this->field->attributes->id);
            $this->content($this->getText());
            $this->appendRequiredFieldIndicator();
        }

    }

     /**
     * Don't render output, if label should not be displayed.
     *
     * @param string $output
     */
    protected function manipulateOutput(string &$output)
    {
        if (!$this->displayLabel || !$this->hasLabel()) {
            $output = '';
        }
    }   

    /**
     * Returns the label-text.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Is a label-text present?
     *
     * @return string
     */
    public function hasLabel()
    {
        return strlen($this->text) > 0;
    }

    /**
     * Do not display label.
     */
    public function hideLabel()
    {
        $this->displayLabel = false;
    }

    private function appendRequiredFieldIndicator()
    {

        if ($this->displayRequiredFieldIndicator) {

            $isVueEnabled = $this->field->isVueEnabled();

            // If vue is enabled, we always render the RequiredFieldIndicator, since it will be reactive.
            if ($this->field->attributes->required || $isVueEnabled) {

                $requiredFieldIndicator = new RequiredFieldIndicator($this->field);

                if ($isVueEnabled) {
                    $requiredFieldIndicator->vIf( "fields['".$this->field->getFieldName()."'].isRequired");
                }

                $this->appendContent($requiredFieldIndicator);
            }

        }
    }

}