<?php

namespace Nicat\FormBuilder\Components;

use Nicat\FormBuilder\AutoTranslation\AutoTranslationInterface;
use Nicat\FormBuilder\Elements\RadioInputElement;
use Nicat\FormBuilder\Elements\Traits\CanHaveErrors;
use Nicat\FormBuilder\Elements\Traits\CanHaveLabel;
use Nicat\FormBuilder\Elements\Traits\UsesAutoTranslation;
use Nicat\HtmlBuilder\Elements\FieldsetElement;

class RadioGroupComponent extends FieldsetElement implements AutoTranslationInterface
{

    use UsesAutoTranslation;

    /**
     * Any errors for radio-buttons contained in this radio-group will be displayed here.
     *
     * @var ErrorWrapper
     */
    private $errorWrapper;

    /**
     * Field-name of the contained radio-buttons.
     *
     * @var string
     */
    protected $radioName;

    /**
     * RadioGroupComponent constructor.
     *
     * @param string $name
     * @param RadioInputElement[] $radioInputElements
     */
    public function __construct(string $name, array $radioInputElements)
    {
        parent::__construct();
        $this->radioName = $name;

        // Set name for all radio-buttons.
        foreach ($radioInputElements as $radioInputElement) {
            $radioInputElement->name($name);
        }

        // Set radio-buttons as content.
        $this->content($radioInputElements);

        // Set $this->errorWrapper and prepend it.
        $this->errorWrapper = (new ErrorWrapper());
        $this->errorWrapper->addErrorField($name);
        $this->prependChild($this->errorWrapper);

        // Auto-translate legend.
        $this->legend($this->performAutoTranslation($this->radioName));
    }

    /**
     * Gets called after applying decorators to the child-elements.
     * Overwrite to perform manipulations.
     */
    protected function afterChildrenDecoration()
    {
        foreach ($this->getChildrenByClassName(\Nicat\HtmlBuilder\Elements\RadioInputElement::class) as $childKey => $child) {
            /** @var CanHaveErrors $child */
            $child->showErrors(false);
        }
    }

    /**
     * Returns the base translation-key for auto-translations for this object.
     *
     * @return string
     */
    function getAutoTranslationKey(): string
    {
        return $this->radioName;
    }
}