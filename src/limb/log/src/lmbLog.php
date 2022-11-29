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
    protected $logs = array();
    protected $log_writers = array();
    protected $level = PHP_INT_MAX;

    protected $backtrace_depth = array(
        LOG_EMERG  => 5,
        LOG_ALERT  => 3,
        LOG_CRIT  => 5,
        LOG_ERR     => 5,
        LOG_WARNING => 1,
        LOG_NOTICE  => 1,
        LOG_INFO    => 3,
        LOG_DEBUG  => 5,
    );

    function registerWriter($writer)
    {
        $this->log_writers[] = $writer;
    }

    function getWriters()
    {
        return $this->log_writers;
    }

    function resetWriters()
    {
        $this->log_writers = array();
    }

    function isLogEnabled()
    {
        return (bool) lmbEnv::get('LIMB_LOG_ENABLE', true);
    }

    function setErrorLevel($level)
    {
        $this->level = $level;
    }

    function setBacktraceDepth($log_level, $depth) {
        $this->backtrace_depth[$log_level] = $depth;
    }

    public function emergency($message, array $context = array())
    {
        $this->log(LOG_EMERG, $message, $context);
    }

    public function alert($message, array $context = array())
    {
        $this->log(LOG_ALERT, $message, $context);
    }

    public function critical($message, array $context = array())
    {
        $this->log(LOG_CRIT, $message, $context);
    }

    public function error($message, array $context = array())
    {
        $this->log(LOG_ERR, $message, $context);
    }

    public function warning($message, array $context = array())
    {
        $this->log(LOG_WARNING, $message, $context);
    }

    public function notice($message, array $context = array())
    {
        $this->log(LOG_NOTICE, $message, $context);
    }

    public function info($message, array $context = array())
    {
        $this->log(LOG_INFO, $message, $context);
    }

    public function debug($message, array $context = array())
    {
        $this->log(LOG_DEBUG, $message, $context);
    }

    public function log($level, $message, $context = array(), $backtrace = null)
    {
        if(!$this->isLogEnabled())
            return;

        if(!$backtrace)
            $backtrace = new lmbBacktrace($this->backtrace_depth[$level]);

        $this->_write($level, $message, $context, $backtrace);
    }

    function logException($exception)
    {
        if(!$this->isLogEnabled())
            return;

        $backtrace_depth = $this->backtrace_depth[LOG_ERR];

        if($exception instanceof lmbException)
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
                array(),
                new lmbBacktrace($exception->getTrace(), $backtrace_depth)
            );
    }

    protected function _write($level, $string, $context = array(), $backtrace = null)
    {
        if(!$this->_isAllowedLevel($level))
            return;

        $entry = new lmbLogEntry($level, $string, $context, $backtrace);
        $this->logs[] = $entry;

        $this->_writeLogEntry($entry);
    }

    protected function _isAllowedLevel($level)
    {
        return $level <= $this->level;
    }

    protected function _writeLogEntry($entry)
    {
        foreach($this->log_writers as $writer)
            $writer->write($entry);
    }
}
