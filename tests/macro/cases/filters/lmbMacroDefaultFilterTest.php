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
