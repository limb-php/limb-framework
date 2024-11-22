<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\filters;

use tests\macro\cases\lmbBaseMacroTestCase;

class lmbMacroUcFirstFilterTest extends lmbBaseMacroTestCase
{
    function testSimple()
    {
        $code = '{$#var|ucfirst}';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $tpl->set('var', 'hello');
        $out = $tpl->render();
        $this->assertEquals('Hello', $out);
    }
}
