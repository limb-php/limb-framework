<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

use limb\validation\src\lmbErrorList;
use limb\core\src\lmbSet;

Mock::generate('lmbErrorList', 'MockErrorList');

abstract class lmbValidationRuleTestCase extends TestCase
{
  protected $error_list;

  function setUp()
  {
    $this->error_list = new MockErrorList();
  }
}

