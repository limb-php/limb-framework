<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\filters;

use tests\macro\cases\lmbBaseMacroTestCase;

class lmbMacroDateFilterTest extends lmbBaseMacroTestCase
{
    function testSimple()
    {
        $code = '{$#var|date:"Y-m-d"}';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $time = mktime(0, 0, 0, 5, 2, 2007);
        $tpl->set('var', $time);
        $out = $tpl->render();
        $this->assertEquals('2007-05-02', $out);
    }
}
