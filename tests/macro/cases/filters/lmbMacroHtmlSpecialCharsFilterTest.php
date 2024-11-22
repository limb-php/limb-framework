<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\filters;

use tests\macro\cases\lmbBaseMacroTestCase;

class lmbMacroHtmlSpecialCharsFilterTest extends lmbBaseMacroTestCase
{
    function testSimple()
    {
        $code = '{$#var|htmlspecialchars}';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $tpl->set('var', '<hello>');
        $out = $tpl->render();
        $this->assertEquals('&lt;hello&gt;', $out);
    }
}
