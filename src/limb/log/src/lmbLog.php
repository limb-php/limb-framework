<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\log\src;

use limb\core\src\lmbEnv;
use limb\core\src\lmbBacktrace;
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
    protected $log_writers = []; // 'writer' => class, 'level' => []

    protected $log_levels = [
        LogLevel::EMERGENCY => 0,
        LogLevel::ALERT => 1,
        LogLevel::CRITICAL => 2,
        LogLevel::ERROR => 3,
        LogLevel::WARNING => 4,
        LogLevel::NOTICE => 5,
        LogLevel::INFO => 6,
        LogLevel::DEBUG => 7,
    ];

    protected $backtrace_depth = [
        LogLevel::DEBUG => 0,
        LogLevel::INFO => 0,
        LogLevel::NOTICE => 0,
        LogLevel::WARNING => 0,
        LogLevel::ERROR => 10,
        LogLevel::CRITICAL => 10,
        LogLevel::ALERT => 5,
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

    function registerWriter($writer, $writer_level = null): void
    {
        $this->log_writers[] = [
            'writer' => $writer,
            'level' => $writer_level
        ];
    }

    function getWriters()
    {
        $writers = [];
        foreach ($this->log_writers as $writer_info) {
            $writers[] = $writer_info['writer'];
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
     * @param string $level
     * @param string $base
     *
     * @return bool
     */
    protected function aboveLevel($level, $base): bool
    {
        return $this->log_levels[$level] <= $this->log_levels[$base];
    }

    function getBacktraceDepth($log_level): int
    {
        return $this->backtrace_depth[$log_level];
    }

    function setBacktraceDepth($log_level, $depth): void
    {
        $this->backtrace_depth[$log_level] = $depth;
    }

    public function emergency($message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical($message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error($message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning($message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice($message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info($message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug($message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    public function log($level, $message, $context = [], $backtrace = null): void
    {
        if ($this->aboveLevel($level, $this->notifyLevel)) {
            $this->_write($level, $message, $context, $backtrace);
        }
    }

    /** @TODO: remove one of aboveLevel() methods */
    /** @TODO: PSR-3: remove $backtrace */
    /** @param $backtrace \limb\core\src\lmbBacktrace */
    protected function _write($level, $string, $context = [], $backtrace = null)
    {
        if (!$backtrace)
            $backtrace = new lmbBacktrace($this->backtrace_depth[$level]);

        $entry = new lmbLogEntry($level, $string, $context, $backtrace);

        $this->_writeLogEntry($entry, $level);
    }

    protected function _writeLogEntry($entry, $level)
    {
        foreach ($this->log_writers as $writer_info) {
            $writer = $writer_info['writer'];
            $writer_level = $writer_info['level'] ?? $this->notifyLevel;

            if ($this->aboveLevel($level, $writer_level)) {
                $writer->write($entry);
            }
        }
    }
}
