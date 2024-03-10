<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\validation\cases;

use PHPUnit\Framework\TestCase;
use limb\validation\src\lmbErrorList;
use limb\validation\src\exception\lmbValidationException;

class lmbValidationExceptionTest extends TestCase
{
    function testErrorListAttachedToErrorMessage()
    {
        $error_list = new lmbErrorList();
        $error_list->addError('error1');
        $error_list->addError('error2');
        $exception = new lmbValidationException('Message.', $error_list, $params = array());
        $this->assertEquals('Message. Errors list : error1, error2', $exception->getMessage());
    }
}

