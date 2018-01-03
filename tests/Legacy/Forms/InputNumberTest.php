<?php
/**
 * Created by PhpStorm.
 * User: geraldb
 * Date: 29.10.2015
 * Time: 13:24
 */

namespace FormBuilderTests\Legacy\Forms;

use FormBuilderTests\Legacy\Traits\Tests\TestsAutocompleteAttribute;
use FormBuilderTests\Legacy\Traits\Tests\TestsValueAttribute;

class InputNumberTest extends InputTestCase
{
    use TestsValueAttribute;

    protected $tagFunction = 'number';

    protected $matchTagAttributes = ['type' => 'number'];
}