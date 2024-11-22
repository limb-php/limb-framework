<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\filters;

use tests\macro\cases\lmbBaseMacroTestCase;

class lmbMacroTrimFilterTest extends lmbBaseMacroTestCase
{
    function testNoParams()
    {
        $code = '{$#var|trim}';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $tpl->set('var', '  hello  ');
        $out = $tpl->render();
        $this->assertEquals('hello', $out);
    }

    function testWithParam()
    {
        $code = '{$#var|trim:"/"}';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $tpl->set('var', '/hello/');
        $out = $tpl->render();
        $this->assertEquals('hello', $out);
    }
}
