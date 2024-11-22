<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\filters;

use tests\macro\cases\lmbBaseMacroTestCase;

class lmbMacroDefaultFilterTest extends lmbBaseMacroTestCase
{
    function testNotDefinedLocalVariable()
    {
        $code = '{$var|default:"val"}';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $out = $tpl->render();
        $this->assertEquals('val', $out);
    }
}
