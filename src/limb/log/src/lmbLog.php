<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\log\src;

use limb\core\src\lmbEnv;
use limb\core\src\lmbBacktrace;
use limb\core\src\exception\lmbException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * class lmbLog.
 *
 * @package log
 * @version $Id$
 */
class lmbLog implements LoggerInterface
{
    protected $notifyLevel;

    protected $logs = [];
    protected $log_writers = []; // 'writer' => class, 'allowed_levels' => []

    protected $log_levels = array(
        LogLevel::EMERGENCY => 0,
        LogLevel::ALERT => 1,
        LogLevel::CRITICAL => 2,
        LogLevel::ERROR => 3,
        LogLevel::WARNING => 4,
        LogLevel::NOTICE => 5,
        LogLevel::INFO => 6,
        LogLevel::DEBUG => 7,
    );

    protected $backtrace_depth = [
        LogLevel::DEBUG => 0,
        LogLevel::INFO => 0,
        LogLevel::NOTICE => 1,
        LogLevel::WARNING => 1,
        LogLevel::ERROR => 5,
        LogLevel::CRITICAL => 5,
        LogLevel::ALERT => 3,
        LogLevel::EMERGENCY => 5
    ];

    public function __construct()
    {
        $this->notifyLevel = lmbEnv::get('LIMB_LOG_LEVEL', LogLevel::NOTICE);
    }

    /**
     * Set the notifyLevel of the logger, as defined in Psr\Log\LogLevel.
     *
     * @param int $notifyLevel
     *
     * @return void
     */
    public function setNotifyLevel($notifyLevel): void
    {
        $this->notifyLevel = $notifyLevel;
    }

    function registerWriter($writer_name, $writer, $allowed_levels = [])
    {
        $this->log_writers[$writer_name] = [
            'writer' => $writer,
            'allowed_levels' => $allowed_levels
        ];
    }

    function getWriters()
    {
        $writers = [];
        foreach ($this->log_writers as $writer_name => $writer_info) {
            $writers[$writer_name] = $writer_info['writer'];
        }

        return $writers;
    }

    function resetWriters()
    {
        $this->log_writers = [];
    }

    /**
     * Checks whether the selected level is above another level.
     *
     * @param mixed $level
     * @param string $base
     *
     * @return bool
     */
    protected function aboveLevel($level, $base): bool
    {
        //$levelOrder = array_keys($this->log_levels);
        //$baseIndex = array_search($base, $levelOrder);
        //$levelIndex = array_search($level, $levelOrder);
        //return $levelIndex >= $baseIndex;

        return $this->log_levels[$level] <= $this->log_levels[$base];
    }

    function getBacktraceDepth($log_level): int
    {
        return $this->backtrace_depth[$log_level];
    }

    function setBacktraceDepth($log_level, $depth)
    {
        $this->backtrace_depth[$log_level] = $depth;
    }

    public function emergency($message, array $context = [])
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = [])
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical($message, array $context = [])
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error($message, array $context = [])
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning($message, array $context = [])
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice($message, array $context = [])
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info($message, array $context = [])
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug($message, array $context = [])
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    public function log($level, $message, $context = [], $backtrace = null)
    {
        if (!$this->aboveLevel($level, $this->notifyLevel)) {
            return;
        }

        if (!$backtrace)
            $backtrace = new lmbBacktrace($this->backtrace_depth[$level]);

        $this->_write($level, $message, $context, $backtrace);
    }

    function logException($exception)
    {
        if (!$this->aboveLevel(LogLevel::ERROR, $this->notifyLevel)) {
            return;
        }

        $backtrace_depth = $this->backtrace_depth[LogLevel::ERROR];

        if ($exception instanceof lmbException)
            $this->log(
                LogLevel::ERROR,
                $exception->getMessage(),
                $exception->getParams(),
                new lmbBacktrace($exception->getTrace(), $backtrace_depth)
            );
        else
            $this->log(
                LogLevel::ERROR,
                $exception->getMessage(),
                [
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine()
                ],
                new lmbBacktrace($exception->getTrace(), $backtrace_depth)
            );
    }

    protected function _write($level, $string, $context = [], $backtrace = null)
    {
        $entry = new lmbLogEntry($level, $string, $context, $backtrace);
        $this->logs[] = $entry;

        $this->_writeLogEntry($entry, $level);
    }

    protected function _writeLogEntry($entry, $level)
    {
        foreach ($this->log_writers as $writer_info) {
            $writer = $writer_info['writer'];
            $allowed_levels = $writer_info['allowed_levels'];

            if ($this->_isAllowedLevel($level, $allowed_levels)) {
                $writer->write($entry);
            }
        }
    }

    protected function _isAllowedLevel($level, $allowed_levels): bool
    {
        if (empty($allowed_levels))
            return true;

        return in_array($level, $allowed_levels);
    }
}
