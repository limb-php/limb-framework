<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\web_app\cases\plain\util;

use limb\web_app\src\util\lmbMessageBox;

class lmbMessageBoxTest extends TestCase
{
  function testGetUnitifiedList()
  {
    $message_box = new lmbMessageBox();
    $message_box->addMessage('Message1');
    $message_box->addError('Error1');
    $message_box->addMessage('Message2');
    $message_box->addError('Error2');
    
    $list = $message_box->getUnifiedList();
    $this->assertEquals(sizeof($list), 4);
    $this->assertEquals($list[0], array('message' => 'Error1', 'is_error' => true, 'is_message' => false));
    $this->assertEquals($list[1], array('message' => 'Error2', 'is_error' => true, 'is_message' => false));
    $this->assertEquals($list[2], array('message' => 'Message1', 'is_error' => false, 'is_message' => true));
    $this->assertEquals($list[3], array('message' => 'Message2', 'is_error' => false, 'is_message' => true));

    // message box is cleaned after  getUnifiedList() is called   
    $list = $message_box->getUnifiedList();
    $this->assertEquals(sizeof($list), 0);
  }
}

