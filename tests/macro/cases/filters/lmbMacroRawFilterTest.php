<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\filters;

use tests\macro\cases\lmbBaseMacroTestCase;

class lmbMacroRawFilterTest extends lmbBaseMacroTestCase
{
    function testSimple()
    {
        $code = '{$#var|raw}';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $tpl->set('var', '<>');
        $out = $tpl->render();
        $this->assertEquals('<>', $out);
    }
}
