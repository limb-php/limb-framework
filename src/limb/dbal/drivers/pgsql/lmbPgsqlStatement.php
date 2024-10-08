<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\drivers\pgsql;

use limb\dbal\drivers\lmbDbStatementInterface;
use limb\dbal\exception\lmbDbException;

/**
 * class lmbPgsqlStatement.
 *
 * @package dbal
 * @version $Id: lmbPgsqlStatement.php 7486 2009-01-26 19:13:20Z
 */
class lmbPgsqlStatement implements lmbDbStatementInterface
{
    protected $statement;
    protected $statement_name;
    protected $connection;
    protected $sql;
    protected $queryId;
    protected $parameters = array();
    protected $prepParams = array();

    function __construct(lmbPgsqlConnection $connection, $sql)
    {
        $this->connection = $connection;
        $this->sql = $sql;
    }

    function setConnection($connection)
    {
        $this->connection = $connection;
    }

    function setNull($name)
    {
        $this->parameters[$name] = null;
    }

    function setBit($name, $value)
    {
        $this->parameters[$name] = is_null($value) ? null : decbin($value); // intval
    }

    function setSmallInt($name, $value)
    {
        if ($value && !is_numeric($value))
            throw new lmbDbException("Can't convert given value to the small int", array('value' => $value));
        $this->parameters[$name] = !is_numeric($value) ? null : intval($value);
    }

    function setInteger($name, $value)
    {
        if ($value && !is_numeric($value))
            throw new lmbDbException("Can't convert given value to the integer", array('value' => $value));
        $this->parameters[$name] = !is_numeric($value) ? null : intval($value);
    }

    function setFloat($name, $value)
    {
        if ($value && !is_numeric($value))
            throw new lmbDbException("Can't convert given value to the float", array('value' => $value));
        $this->parameters[$name] = !is_numeric($value) ? null : floatval($value);
    }

    function setDouble($name, $value)
    {
        if (is_float($value) || is_integer($value)) {
            $this->parameters[$name] = $value;
        } else if (is_string($value) && preg_match('/^(|-)\d+(|.\d+)$/', $value)) {
            $this->parameters[$name] = $value;
        } else if (!$value) {
            $this->parameters[$name] = null;
        } else {
            throw new lmbDbException("Can't convert given value to the double", array('value' => $value));
        }
    }

    function setDecimal($name, $value)
    {
        if (is_float($value) || is_integer($value)) {
            $this->parameters[$name] = $value;
        } else if (is_string($value) && preg_match('/^(|-)\d+(|.\d+)$/', $value)) {
            $this->parameters[$name] = $value;
        } else if (!$value) {
            $this->parameters[$name] = null;
        } else {
            throw new lmbDbException("Can't convert given value to the decimal", array('value' => $value));
        }
    }

    function setBoolean($name, $value)
    {
        $this->parameters[$name] = is_null($value) ? null : (($value) ? "1" : "0");
    }

    function setChar($name, $value)
    {
        $this->parameters[$name] = is_null($value) ? null : $value;
    }

    function setVarChar($name, $value)
    {
        $this->parameters[$name] = is_null($value) ? null : $value;
    }

    function setClob($name, $value)
    {
        $this->parameters[$name] = is_null($value) ? null : $value;
    }

    protected function _setDate($name, $value, $format)
    {
        if (is_int($value)) {
            $this->parameters[$name] = date($format, $value);
        } else if (is_string($value)) {
            $this->parameters[$name] = (string)$value;
        } else {
            $this->parameters[$name] = null;
        }
    }


    function setDate($name, $value)
    {
        $this->_setDate($name, $value, 'Y-m-d');
    }

    function setTime($name, $value)
    {
        $this->_setDate($name, $value, 'H:i:s');
    }

    function setTimeStamp($name, $value)
    {
        $this->_setDate($name, $value, 'Y-m-d H:i:s');
    }


    function setBlob($name, $value)
    {
        $this->parameters[$name] = is_null($value) ? null : pg_escape_bytea($this->connection->getConnectionId(), $value);
    }

    function set($name, $value)
    {
        if (is_string($value)) {
            $this->setChar($name, $value);
        } else if (is_int($value)) {
            $this->setInteger($name, $value);
        } else if (is_bool($value)) {
            $this->setBoolean($name, $value);
        } else if (is_float($value)) {
            $this->setFloat($name, $value);
        } else {
            $this->setNull($name);
        }
    }

    function import($paramList)
    {
        foreach ($paramList as $name => $value) {
            $this->set($name, $value);
        }
    }

    protected function _prepareStatement()
    {
        $sql = $this->_handleBindVars($this->sql);
        $this->generateStatementName();

       $this->statement = pg_prepare($this->connection->getConnectionId(), $this->getStatementName(), $sql);
       if (!$this->statement) {
            $this->connection->_raiseError("Error while preparing statement");
        }
    }


    protected function _handleBindVars($sql)
    {
        $this->prepParams = array();
        $newsql = '';
        $index = 1;
        if (preg_match("#^select :(\w+):;?$#i", $sql, $m))
            $cast_types = true;
        else
            $cast_types = false;
        while (preg_match('/^(\'[^\']*?\')|(--[^(\n)]*?\n)|(:(?-U)\w+:(?U))|.+/Us', $sql, $matches)) {
            if (isset($matches[3])) {
                $param = str_replace(':', '', $matches[0]);

                if (!array_key_exists($param, $this->parameters))
                    $this->parameters[$param] = null;

                $this->prepParams[] = $this->parameters[$param];
                $newsql .= '$' . $index;
                if ($cast_types) {
                    $this->statement_name = null;
                    $newsql .= '::';
                    if (is_null($this->parameters[$param])) {
                        $newsql .= 'int';
                    } elseif (is_integer($this->parameters[$param])) {
                        $newsql .= 'int';
                    } elseif (is_float($this->parameters[$param])) {
                        $newsql .= 'float';
                    } else {
                        $newsql .= 'varchar';
                    }
                }
                $index++;
            } else
                $newsql .= $matches[0];

            $sql = substr($sql, strlen($matches[0]));
        }
        return $newsql;
    }

    function getSQL()
    {
        return $this->sql;
    }

    function getParameters()
    {
        return $this->parameters;
    }

    function getPrepParams()
    {
        return $this->prepParams;
    }

    function getStatementName()
    {
        return $this->statement_name;
    }

    function generateStatementName(): string
    {
        $this->connection->incStatementNumber();

        return $this->statement_name = "pgsql_statement_" . $this->connection->getStatementNumber();
    }

    function execute($sql = "")
    {
        if (!empty($sql)) {
            $stored_sql = $this->sql;
            $this->sql = $sql;
            $this->free();
        }

        $this->_prepareStatement();
        $queryId = $this->connection->executeStatement($this);

        if (isset($stored_sql)) {
            $this->sql = $stored_sql;
            $this->free();
        }
        return $queryId;
    }

    function addOrder($sort_params)
    {
        if (preg_match('~(?<=FROM).+\s+ORDER\s+BY\s+~i', $this->sql))
            $this->sql .= ',';
        else
            $this->sql .= ' ORDER BY ';

        foreach ($sort_params as $field => $order)
            $this->sql .= $this->connection->quoteIdentifier($field) . " $order,";

        $this->sql = rtrim($this->sql, ',');
    }

    function addLimit($offset, $limit)
    {
        $this->sql .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
    }

    function count()
    {
        if (!$this->queryId)
            $this->queryId = $this->execute();

        return pg_num_rows($this->queryId);
    }

    function free()
    {
        if ($this->queryId && lmbPgsqlConnection::checkPgResult($this->queryId))
            pg_free_result($this->queryId);

        $this->queryId = null;
        $this->statement = null;
        $this->statement_name = null;
    }

}
