<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\web_app\cases\plain\util;

use PHPUnit\Framework\TestCase;
use limb\web_app\src\util\lmbMessageBox;

require_once dirname(__FILE__) . '/../../init.inc.php';

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
        $this->assertEquals(4, sizeof($list));
        $this->assertEquals(array('message' => 'Error1', 'is_error' => true, 'is_message' => false, 'type' => 'error'), $list[0]);
        $this->assertEquals(array('message' => 'Error2', 'is_error' => true, 'is_message' => false, 'type' => 'error'), $list[1]);
        $this->assertEquals(array('message' => 'Message1', 'is_error' => false, 'is_message' => true, 'type' => 'message'), $list[2]);
        $this->assertEquals(array('message' => 'Message2', 'is_error' => false, 'is_message' => true, 'type' => 'message'), $list[3]);

        // message box is cleaned after  getUnifiedList() is called
        $list = $message_box->getUnifiedList();
        $this->assertEquals(0, sizeof($list));
    }
}
