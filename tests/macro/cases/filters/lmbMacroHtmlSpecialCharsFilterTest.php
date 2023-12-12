<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\macro\cases\filters;

use Tests\macro\cases\lmbBaseMacroTestCase;

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
