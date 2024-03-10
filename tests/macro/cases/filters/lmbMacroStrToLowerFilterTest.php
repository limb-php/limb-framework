<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\filters;

use tests\macro\cases\lmbBaseMacroTestCase;

class lmbMacroStrToLowerFilterTest extends lmbBaseMacroTestCase
{
    function testSimple()
    {
        $code = '{$#var|strtolower}';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $tpl->set('var', 'HELLO');
        $out = $tpl->render();
        $this->assertEquals('hello', $out);
    }

    function testAlias()
    {
        $code = '{$#var|lowercase}';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $tpl->set('var', 'HELLO');
        $out = $tpl->render();
        $this->assertEquals('hello', $out);
    }
}
