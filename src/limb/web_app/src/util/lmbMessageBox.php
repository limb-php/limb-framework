<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_app\src\util;

/**
 * class lmbMessageBox.
 *
 * @package web_app
 * @version $Id: lmbMessageBox.php 7486 2009-01-26 19:13:20Z
 */
class lmbMessageBox
{
    const ERRORS = 1;
    const MESSAGES = 2;

    protected $messages = [];

    function __construct()
    {
        $this->reset();
    }

    function reset()
    {
        $this->messages = [
            self::ERRORS => [],
            self::MESSAGES => []
        ];
    }

    function resetMessages()
    {
        $this->messages[self::MESSAGES] = [];
    }

    function resetErrors()
    {
        $this->messages[self::ERRORS] = [];
    }

    function addError($error)
    {
        $this->messages[self::ERRORS][] = $error;
    }

    function addMessage($message)
    {
        $this->messages[self::MESSAGES][] = $message;
    }

    function getErrors()
    {
        return $this->messages[self::ERRORS];
    }

    function getMessages()
    {
        return $this->messages[self::MESSAGES];
    }

    function hasErrors(): bool
    {
        return sizeof($this->messages[self::ERRORS]) > 0;
    }

    function hasMessages(): bool
    {
        return sizeof($this->messages[self::MESSAGES]) > 0;
    }

    function getUnifiedList(): array
    {
        $result = [];

        foreach ($this->getErrors() as $error)
            $result[] = ['message' => $error, 'is_error' => true, 'is_message' => false, 'type' => 'error'];

        foreach ($this->getMessages() as $message)
            $result[] = ['message' => $message, 'is_message' => true, 'is_error' => false, 'type' => 'message'];

        $this->reset();

        return $result;
    }
}
