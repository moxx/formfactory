<?php

namespace FormFactoryTests\Feature\Components\FormControls\Fields\Raw;

use FormFactoryTests\TestCase;

class PasswordInputTest extends TestCase
{

    protected $viewBase = 'raw';

    public function testSimple()
    {
        $element = \Form::password('myFieldName');

        $this->assertHtmlEquals(
            '
                <div>
                    <label for="myFormId_myFieldName">MyFieldName</label>
                    <input type="password" name="myFieldName" id="myFormId_myFieldName" placeholder="MyFieldName" />
                </div>
            ',
            $element->generate()
        );
    }

    public function testComplex()
    {
        $element = \Form::password('myFieldName')
            ->helpText('myHelpText')
            ->errors(['myFirstError', 'mySecondError'])
            ->rules('required|alpha|max:10');

        $this->assertHtmlEquals(
            '
                <div>
                    <label for="myFormId_myFieldName">MyFieldName<sup>*</sup></label>
                    <div role="alert" id="myFormId_myFieldName_errors">
                        <div>myFirstError</div>
                        <div>mySecondError</div>
                    </div>
                    <input type="password" name="myFieldName" id="myFormId_myFieldName" required aria-describedby="myFormId_myFieldName_errors myFormId_myFieldName_helpText" aria-invalid="true" pattern="[a-zA-Z]+" maxlength="10" placeholder="MyFieldName" />
                    <small id="myFormId_myFieldName_helpText">myHelpText</small>
                </div>
            ',
            $element->generate()
        );
    }

}