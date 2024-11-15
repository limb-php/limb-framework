<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\log\src;

use limb\core\src\lmbSys;
use Psr\Log\LogLevel;

/**
 * class lmbLogEntry.
 *
 * @package log
 * @version $Id$
 */
class lmbLogEntry
{
    protected $level;
    protected $time;
    protected $message;
    protected $params;
    protected $backtrace;
    protected $names_map = array(
        LogLevel::EMERGENCY => 'Emergency',
        LogLevel::ALERT => 'Alert',
        LogLevel::CRITICAL => 'Critical',
        LogLevel::ERROR => 'Error',
        LogLevel::WARNING => 'Warning',
        LogLevel::NOTICE => 'Notice',
        LogLevel::INFO => 'Info',
        LogLevel::DEBUG => 'Debug',
    );

    /** @param $backtrace \limb\core\src\lmbBacktrace */
    function __construct($level, $message, $params = array(), $backtrace = null, $time = null)
    {
        $this->level = $level;
        $this->message = $message;
        $this->params = $params;
        $this->backtrace = $backtrace;
        $this->time = !$time ? time() : $time;
    }

    function getLevel()
    {
        return $this->level;
    }

    function getMessage()
    {
        return $this->message;
    }

    function getTime()
    {
        return $this->time;
    }

    function getParams()
    {
        return $this->params;
    }

    function getBacktrace()
    {
        return $this->backtrace;
    }

    function isLevel($level)
    {
        return $this->level == $level;
    }

    function getLevelForHuman()
    {
        return $this->names_map[$this->level];
    }

    function toString()
    {
        return lmbSys::isCli() ? $this->asText() : $this->asHtml();
    }

    function asText()
    {
        $params = $this->params;
        unset($params['exception']);

        $string = $this->getLevelForHuman() . " message: {$this->message}";
        $string .= (count($params) ? "\nAdditional attributes: " . var_export($params, true) : '');
        if ($this->backtrace && $backtrace_str = $this->backtrace->toString())
            $string .= "\nBack trace:\n" . $backtrace_str;

        return $string;
    }

    function asHtml()
    {
        return '<pre>' . htmlspecialchars($this->asText()) . '</pre>';
    }
}
