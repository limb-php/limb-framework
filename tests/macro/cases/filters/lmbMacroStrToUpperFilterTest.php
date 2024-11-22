<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\filters;

use tests\macro\cases\lmbBaseMacroTestCase;

class lmbMacroStrToUpperFilterTest extends lmbBaseMacroTestCase
{
    function testSimple()
    {
        $code = '{$#var|strtoupper}';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $tpl->set('var', 'hello');
        $out = $tpl->render();
        $this->assertEquals('HELLO', $out);
    }
}
