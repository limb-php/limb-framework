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

class lmbMacroWordDeclensionFilterTest extends lmbBaseMacroTest
{
  
  function _getUserResultForNumber($number)
  {
    $code = '{$#number|declension:"пользователь", "пользователей", "пользователя"}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('number', $number);
    $out = $tpl->render();
    return $out;
  }
  
  function testFunction()
  {
    $this->assertEquals($this->_getUserResultForNumber(1), "пользователь");
    $this->assertEquals($this->_getUserResultForNumber('1'), "пользователь");
    $this->assertEquals($this->_getUserResultForNumber(2), "пользователя");
    $this->assertEquals($this->_getUserResultForNumber(11), "пользователей");
    $this->assertEquals($this->_getUserResultForNumber(12), "пользователей");
    $this->assertEquals($this->_getUserResultForNumber(22), "пользователя");
    $this->assertEquals($this->_getUserResultForNumber(235), "пользователей");
    $this->assertEquals($this->_getUserResultForNumber(10001), "пользователь");
  }
}

