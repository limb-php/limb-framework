<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\filters;

use tests\macro\cases\lmbBaseMacroTestCase;

class lmbMacroNumberFormatFilterTest extends lmbBaseMacroTestCase
{
    function testNoParams()
    {
        $code = '{$#var|number_format}';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $tpl->set('var', 1234.56);
        $out = $tpl->render();
        $this->assertEquals('1,235', $out);
    }

    function testWithParams()
    {
        $code = '{$#var|number_format:2,","," "}';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $tpl->set('var', 1234.56);
        $out = $tpl->render();
        $this->assertEquals('1 234,56', $out);
    }

    function testAlias()
    {
        $code = '{$#var|number:2,","," "}';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $tpl->set('var', 1234.56);
        $out = $tpl->render();
        $this->assertEquals('1 234,56', $out);
    }
}
