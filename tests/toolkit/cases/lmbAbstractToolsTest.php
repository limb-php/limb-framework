<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\toolkit\cases;

use limb\toolkit\src\lmbAbstractTools;
use PHPUnit\Framework\TestCase;

class TestAbstractTools extends lmbAbstractTools
{
  function foo(){}
  function bar(){}
}

class lmbAbstractToolsTest extends TestCase
{
  function testGetToolsSignatures()
  {
    $tools = new TestAbstractTools();
    $this->assertEquals($tools->getToolsSignatures(),
                       array('foo' => $tools, 'bar' => $tools));
  }
}


