<?php

namespace FormFactoryTests\Feature\Components\FormControls\Bootstrap4Vue;

class UrlInputTest extends AbstractBootstrap4VueTest
{

    public function testSimple()
    {
        $element = \Form::url('myFieldName');

        $this->assertHtmlEquals(
            '
                <div class="form-group">
                    <label for="myFormId_myFieldName">MyFieldName<template><sup v-if="fields[\'myFieldName\'].isRequired">*</sup></template></label>
		            <template>           
	                    <div class="invalid-feedback" role="alert" id="myFormId_myFieldName_errors" v-if="fieldHasError(\'myFieldName\')">
	                        <div v-for="error in fields[\'myFieldName\'].errors"> {{ error }} </div>
	                    </div>
		            </template>
                    <input type="url" name="myFieldName" class="form-control" id="myFormId_myFieldName" v-bind:aria-describedby="(fieldHasError(\'myFieldName\') ? \'myFormId_myFieldName_errors\' : \'\')" placeholder="MyFieldName" v-model="fields[\'myFieldName\'].value" v-bind:required="fields[\'myFieldName\'].isRequired" v-bind:disabled="fields[\'myFieldName\'].isDisabled" v-bind:class="{ \'is-invalid\': fieldHasError(\'myFieldName\') }" v-bind:aria-invalid="fieldHasError(\'myFieldName\')" />
                </div>
            ',
            $element->generate()
        );
    }

    public function testComplex()
    {
        $element = \Form::url('myFieldName')
            ->helpText('myHelpText')
            ->errors(['myFirstError', 'mySecondError'])
            ->rules('required|alpha|max:10');

        $this->assertHtmlEquals(
            '
                <div class="form-group">
                    <label for="myFormId_myFieldName">MyFieldName<template><sup v-if="fields[\'myFieldName\'].isRequired">*</sup></template></label>
		            <template>           
	                    <div class="invalid-feedback" role="alert" id="myFormId_myFieldName_errors" v-if="fieldHasError(\'myFieldName\')">
	                        <div v-for="error in fields[\'myFieldName\'].errors"> {{ error }} </div>
	                    </div>
		            </template>
                    <input type="url" name="myFieldName" class="form-control" id="myFormId_myFieldName"v-bind:aria-describedby="(fieldHasError(\'myFieldName\') ? \'myFormId_myFieldName_errors\' : \'\')+ \' myFormId_myFieldName_helpText\'" pattern="[a-zA-Z]+" maxlength="10" placeholder="MyFieldName" v-model="fields[\'myFieldName\'].value" v-bind:required="fields[\'myFieldName\'].isRequired" v-bind:disabled="fields[\'myFieldName\'].isDisabled" v-bind:class="{ \'is-invalid\': fieldHasError(\'myFieldName\') }" v-bind:aria-invalid="fieldHasError(\'myFieldName\')" />
                    <small id="myFormId_myFieldName_helpText" class="text-muted form-text small">myHelpText</small>
                </div>
            ',
            $element->generate()
        );
    }

}