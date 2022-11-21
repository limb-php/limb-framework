<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\macro\cases\filters;

use tests\macro\cases\lmbBaseMacroTest;

class lmbMacroDefaultFilterTest extends lmbBaseMacroTest
{
  function testNotDefinedLocalVariable()
  {
    $code = '{$var|default:"val"}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $out = $tpl->render();
    $this->assertEquals($out, 'val');
  }
}
