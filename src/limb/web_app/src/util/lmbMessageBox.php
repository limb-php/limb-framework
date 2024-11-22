<?php
/*
 * Limb PHP Framework
 *
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

    function reset($type = null)
    {
        if($type)
            $this->messages[$type] = [];
        else
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

    function add($type, $message)
    {
        $this->messages[$type][] = $message;
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

        foreach ($this->getErrors() as $message)
            $result[] = ['message' => $message, 'is_error' => true, 'is_message' => false, 'type' => 'error'];

        foreach ($this->getMessages() as $message)
            $result[] = ['message' => $message, 'is_message' => true, 'is_error' => false, 'type' => 'message'];

        $this->reset();

        return $result;
    }
}
