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

/**
 * class lmbLog.
 *
 * @package log
 * @version $Id$
 */
class lmbLog
{
    protected $notifyLevel;

    protected $logs = [];
    protected $log_writers = []; // 'writer' => class, 'allowed_levels' => []

    protected $backtrace_depth = [
        LOG_DEBUG => 5,
        LOG_INFO => 3,
        LOG_NOTICE => 1,
        LOG_WARNING => 1,
        LOG_ERR => 5,
        LOG_CRIT => 5,
        LOG_ALERT => 3,
        LOG_EMERG => 5
    ];

    public function __construct()
    {
        $this->notifyLevel = lmbEnv::get('LIMB_LOG_LEVEL', LOG_NOTICE);
    }

    /**
     * Set the notifyLevel of the logger, as defined in Psr\Log\LogLevel.
     *
     * @param int $notifyLevel
     *
     * @return void
     */
    public function setNotifyLevel(int $notifyLevel): void
    {
        $this->notifyLevel = $notifyLevel;
    }

    function registerWriter($writer, $allowed_levels = [])
    {
        $this->log_writers[] = [
            'writer' => $writer,
            'allowed_levels' => $allowed_levels
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
     * @param mixed  $level
     * @param string $base
     *
     * @return bool
     */
    protected function aboveLevel($level, $base): bool
    {
        $levelOrder = array_keys($this->backtrace_depth);
        $baseIndex = array_search($base, $levelOrder);
        $levelIndex = array_search($level, $levelOrder);

        return $levelIndex >= $baseIndex;
    }

    function setBacktraceDepth($log_level, $depth)
    {
        $this->backtrace_depth[$log_level] = $depth;
    }

    public function emergency($message, array $context = [])
    {
        $this->log(LOG_EMERG, $message, $context);
    }

    public function alert($message, array $context = [])
    {
        $this->log(LOG_ALERT, $message, $context);
    }

    public function critical($message, array $context = [])
    {
        $this->log(LOG_CRIT, $message, $context);
    }

    public function error($message, array $context = [])
    {
        $this->log(LOG_ERR, $message, $context);
    }

    public function warning($message, array $context = [])
    {
        $this->log(LOG_WARNING, $message, $context);
    }

    public function notice($message, array $context = [])
    {
        $this->log(LOG_NOTICE, $message, $context);
    }

    public function info($message, array $context = [])
    {
        $this->log(LOG_INFO, $message, $context);
    }

    public function debug($message, array $context = [])
    {
        $this->log(LOG_DEBUG, $message, $context);
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
        if (!$this->aboveLevel(LOG_ERR, $this->notifyLevel)) {
            return;
        }

        $backtrace_depth = $this->backtrace_depth[LOG_ERR];

        if ($exception instanceof lmbException)
            $this->log(
                LOG_ERR,
                $exception->getMessage(),
                $exception->getParams(),
                new lmbBacktrace($exception->getTrace(), $backtrace_depth)
            );
        else
            $this->log(
                LOG_ERR,
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
