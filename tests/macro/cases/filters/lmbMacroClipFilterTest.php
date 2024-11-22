<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\filters;

use tests\macro\cases\lmbBaseMacroTestCase;

class lmbMacroClipFilterTest extends lmbBaseMacroTestCase
{
    function testStatic()
    {
        $code = '{$#str|clip:1}{$#str|clip:2}{$#str|clip:3}';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $tpl->set('str', '12');
        $out = $tpl->render();
        $this->assertEquals('11212', $out);
    }

    function testDinamic()
    {
        $code = '{$#str|clip:$#cnt}';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $tpl->set('str', 'foo');
        $tpl->set('cnt', '1');
        $out = $tpl->render();
        $this->assertEquals('f', $out);
    }
}
