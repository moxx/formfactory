<?php

namespace Nicat\FormFactory\Components\Additional;

use Nicat\FormFactory\Components\Contracts\FieldInterface;
use Nicat\FormFactory\Components\Contracts\FormControlInterface;
use Nicat\FormFactory\Components\FormControls\RadioInput;
use Nicat\FormFactory\Utilities\FormFactoryTools;
use Nicat\HtmlFactory\Elements\FieldsetElement;
use Nicat\HtmlFactory\Elements\LegendElement;

class FieldsetLegend extends LegendElement
{

    /**
     * The Fieldset this FieldsetLegend belongs to.
     *
     * @var FieldsetElement|FormControlInterface|FieldInterface
     */
    public $fieldset;

    /**
     * The legend-text.
     *
     * @var string
     */
    protected $text;

    /**
     * Should the legend be displayed?
     *
     * @var bool
     */
    public $displayLegend = true;

    /**
     * Should the legend include an indicator for required fields?
     *
     * @var bool
     */
    public $displayRequiredFieldIndicator = true;

    /**
     * FieldLegend constructor.
     *
     * @param FieldsetElement $fieldset
     */
    public function __construct(FieldsetElement $fieldset)
    {
        parent::__construct();
        $this->fieldset = $fieldset;
    }

    /**
     * Sets the legend-text.
     *
     * @param $text
     * @return FieldsetLegend
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    public function beforeDecoration()
    {

        if ($this->displayLegend) {

            // Perform auto-translation, if no legend was manually set.
            if (!$this->hasLegend()) {
                $defaultValue = ucwords(FormFactoryTools::arrayStripString($this->fieldset->getFieldName()));
                if ($this->fieldset->is(RadioInput::class)) {
                    $defaultValue = ucwords($this->fieldset->attributes->value);
                }
                $this->setText(
                    $this->fieldset->performAutoTranslation($defaultValue)
                );
            }

            $this->content($this->getText());
            $this->appendRequiredFieldIndicator();
        }

    }

     /**
     * Don't render output, if legend should not be displayed.
     *
     * @param string $output
     */
    protected function manipulateOutput(string &$output)
    {
        if (!$this->displayLegend || !$this->hasLegend()) {
            $output = '';
        }
    }   

    /**
     * Returns the legend-text.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Is a legend-text present?
     *
     * @return string
     */
    public function hasLegend()
    {
        return strlen($this->text) > 0;
    }

    /**
     * Do not display legend.
     */
    public function hideLegend()
    {
        $this->displayLegend = false;
    }

    private function appendRequiredFieldIndicator()
    {

        if ($this->displayRequiredFieldIndicator) {

            $isVueEnabled = $this->fieldset->isVueEnabled();

            // If vue is enabled, we always render the RequiredFieldIndicator, since it will be reactive.
            if ($this->fieldset->attributes->required || $isVueEnabled) {

                $requiredFieldIndicator = new RequiredFieldIndicator($this->fieldset);

                if ($isVueEnabled) {
                    $requiredFieldIndicator->vIf( "fields['".$this->fieldset->getFieldName()."'].isRequired");
                }

                $this->appendContent($requiredFieldIndicator);
            }

        }
    }

}