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
