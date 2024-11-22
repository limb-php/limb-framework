<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\core\src;

/**
 * class lmbBacktrace.
 *
 * @package log
 * @version $Id$
 */
class lmbBacktrace
{
    protected $backtrace = [];
    protected $limit = null;
    protected $offset = 0;

    function __construct($limit_or_backtrace = null, $limit_or_offset = null, $offset = 0)
    {
        if (is_array($limit_or_backtrace)) {
            $this->backtrace = $limit_or_backtrace;
            $this->limit = $limit_or_offset;
            $this->offset = (int)$offset;
        } else {
            $this->backtrace = debug_backtrace();
            $this->limit = $limit_or_backtrace;
            $this->offset = (int)$limit_or_offset;
        }
    }

    function setBacktrace($backtrace = []): static
    {
        $this->backtrace = $backtrace;

        return $this;
    }

    function setLimit($limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    function setOffset($offset): static
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * get limited backtrace
     * @return array strings of backtrace
     */
    function get(): array
    {
        return $this->_preparedBacktrace();
    }

    function getAll()
    {
        return $this->backtrace;
    }

    function getContext()
    {
        return (sizeof($this->backtrace)) ? $this->backtrace[0] : '';
    }

    function isEmpty(): bool
    {
        return empty($this->backtrace);
    }

    function toString()
    {
        $trace_string = '';

        $backtrace = $this->_preparedBacktrace();

        foreach ($backtrace as $item) {
            $trace_string .= '* ';
            $trace_string .= $this->_formatBacktraceItem($item) . PHP_EOL;
        }
        return $trace_string;
    }

    protected function _preparedBacktrace(): array
    {
        $backtrace = $this->backtrace;

        //we skip this function call also
        for ($i = 0; $i < ($this->offset + 1); $i++)
            array_shift($backtrace);

        if (!is_null($this->limit))
            $backtrace = array_splice($backtrace, 0, $this->limit);

        return $backtrace;
    }

    function _formatBacktraceItem($item)
    {
        $trace_string = '';

        if (isset($item['class'])) {
            $trace_string .= $item['class'];
            $trace_string .= "::";
        }

        if (isset($item['function'])) {
            $trace_string .= $item['function'];
            $trace_string .= "(";
        }

        if (isset($item['args'])) {
            $sep = '';
            foreach ($item['args'] as $arg) {
                $trace_string .= $sep;
                $sep = ', ';

                if (is_null($arg))
                    $trace_string .= 'NULL';
                elseif (is_array($arg))
                    $trace_string .= 'ARRAY[' . sizeof($arg) . ']';
                elseif (is_object($arg))
                    $trace_string .= 'OBJECT:' . get_class($arg);
                elseif (is_bool($arg))
                    $trace_string .= $arg ? 'TRUE' : 'FALSE';
                else {
                    $trace_string .= '"';
                    $trace_string .= htmlspecialchars(substr((string)@$arg, 0, 100));

                    if (is_string($arg) && strlen($arg) > 100)
                        $trace_string .= '...';

                    $trace_string .= '"';
                }
            }
        }

        if (isset($item['function'])) {
            $trace_string .= ")";
        }

        if (isset($item['file'])) {
            $trace_string .= ' in "' . $item['file'] . '"';
            $trace_string .= " line ";
            $trace_string .= $item['line'];
        }

        return $trace_string;
    }
}
