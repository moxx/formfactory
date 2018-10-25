<?php

namespace FormFactoryTests\Feature\Components\FormControls\Fields\Bootstrap4;

use FormFactoryTests\TestCase;

class UrlInputTest extends TestCase
{

    protected $viewBase = 'formfactory::bootstrap4';
    protected $decorators = ['bootstrap:v4'];

    public function testSimple()
    {
        $element = \Form::url('url');

        $this->assertHtmlEquals(
            '
                <div class="form-group">
                    <label for="myFormId_url">Url</label>
                    <input type="url" name="url" id="myFormId_url" class="form-control" placeholder="Url" />
                </div>
            ',
            $element->generate()
        );
    }

    public function testComplex()
    {
        $element = \Form::url('url')
            ->helpText('myHelpText')
            ->errors(['myFirstError', 'mySecondError'])
            ->rules('required|alpha|max:10');

        $this->assertHtmlEquals(
            '
                <div class="form-group has-error">
                    <label for="myFormId_url">Url<sup>*</sup></label>
                    <div id="myFormId_url_errors">
                        <div>myFirstError</div>
                        <div>mySecondError</div>
                    </div>
                    <input type="url" name="url" id="myFormId_url" class="form-control" required pattern="[a-zA-Z]+" maxlength="10" placeholder="Url" aria-describedby="myFormId_url_errors myFormId_url_helpText" aria-invalid="true" />
                    <small id="myFormId_url_helpText" class="form-text text-muted">myHelpText</small>
                </div>
            ',
            $element->generate()
        );
    }

}