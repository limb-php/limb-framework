<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\tags\form;

use tests\macro\cases\lmbBaseMacroTestCase;

class lmbMacroInputRadioTagTest extends lmbBaseMacroTestCase
{

    function testIsChecked_If_ValueAttribute_IsEqual_To_FormDatasourceFieldValue()
    {
        $template = '{{form id="my_form"}}' .
            '{{input type="radio" id="r1" name="my_input" value="foo"/}}' .
            '{{input type="radio" id="r2" name="my_input" value="bar" checked="checked" /}}' .
            '{{/form}}';

        $page = $this->_createMacroTemplate($template, 'tpl.html');
        $page->set('form_my_form_datasource', array("my_input" => 'foo'));

        $expected = '<form id="my_form">' .
            '<input type="radio" id="r1" name="my_input" value="foo" checked="checked" />' .
            '<input type="radio" id="r2" name="my_input" value="bar" />' .
            '</form>';
        $this->assertEquals($page->render(), $expected);
    }

    function testRemoveCheckedIfNotChecked()
    {
        $template = '{{form id="my_form"}}' .
            '{{input type="radio" id="r0" name="my_input" value="0"/}}' .
            '{{input type="radio" id="r1" name="my_input" value="1" checked="checked" /}}' .
            '{{input type="radio" id="r2" name="my_input" value="2"/}}' .
            '{{/form}}';
        $page = $this->_createMacroTemplate($template, 'tpl.html');

        $expected = '<form id="my_form">' .
            '<input type="radio" id="r0" name="my_input" value="0" />' .
            '<input type="radio" id="r1" name="my_input" value="1" checked="checked" />' .
            '<input type="radio" id="r2" name="my_input" value="2" />' .
            '</form>';

        $this->assertEquals($page->render(), $expected);
    }
}
