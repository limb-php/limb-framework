<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\drivers\pgsql;

use limb\dbal\drivers\lmbDbBaseRecord;

/**
 * class lmbPgsqlRecord.
 *
 * @package dbal
 * @version $Id: lmbPgsqlRecord.php 7486 2009-01-26 19:13:20Z
 */
class lmbPgsqlRecord extends lmbDbBaseRecord
{
    function __construct($data = array())
    {
        $this->properties = $data;
    }

    function get($name, $default = null)
    {
        if (isset($this->properties[$name]))
            return $this->properties[$name];

        return $default;
    }

    function set($name, $value)
    {
        $this->properties[$name] = $value;
    }

    function export()
    {
        return $this->properties;
    }

    function import($values)
    {
        $this->properties = $values;
    }

    function remove($name)
    {
        if (isset($this->properties[$name]))
            unset($this->properties[$name]);
    }

    function has($name)
    {
        return isset($this->properties[$name]);
    }

    function reset()
    {
        $this->properties = array();
    }

    function getBit($name)
    {
        $value = $this->get($name);
        return is_null($value) ? null : bindec($value); // int
    }

    function getInteger($name)
    {
        $value = $this->get($name);
        return is_null($value) ? null : (int)$value;
    }

    function getFloat($name)
    {
        $value = $this->get($name);
        return is_null($value) ? null : (float)$value;
    }

    function getString($name)
    {
        $value = $this->get($name);
        return is_null($value) ? null : (string)$value;
    }

    function getBoolean($name)
    {
        $value = $this->get($name);
        if (is_null($value))
            return null;

        return $value == 't' ? true : false;
    }

    function getIntegerTimeStamp($name)
    {
        $value = $this->get($name);
        if (is_integer($value)) {
            return $value;
        } else if (is_string($value)) {
            $ts = strtotime($value);
            if ($ts === -1) {
                if (preg_match('/([\d]{4})([\d]{2})([\d]{2})([\d]{2})([\d]{2})([\d]{2})/', $value, $matches))
                    return mktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1]);
            } else
                return $ts;
        }
    }

    function _getDate($name, $format)
    {
        $value = $this->get($name);
        if (is_integer($value))
            return date($format, $value);
        else
            return $value;
    }

    function getStringDate($name)
    {
        return $this->_getDate($name, 'Y-m-d');
    }

    function getStringTime($name)
    {
        return $this->_getDate($name, 'H:i:s');
    }

    function getStringTimeStamp($name)
    {
        return $this->_getDate($name, 'Y-m-d H:i:s');
    }

    function getStringFixed($name)
    {
        $value = $this->get($name);
        return is_null($value) ? null : (string)$value;
    }

    function getBlob($name)
    {
        $value = $this->get($name);
        return is_null($value) ? null : pg_unescape_bytea($value);
    }

    function getClob($name)
    {
        return $this->get($name);
    }

    function getChar($name)
    {
        return $this->get($name);
    }
}
