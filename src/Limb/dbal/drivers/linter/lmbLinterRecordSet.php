<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\drivers\linter;

use limb\dbal\drivers\lmbDbBaseRecordSet;

/**
 * class lmbLinterRecordSet.
 *
 * @package dbal
 * @version $Id: $
 */
class lmbLinterRecordSet extends lmbDbBaseRecordSet
{
    protected $queryId;
    protected $stmt;
    protected $connection;

    protected $current;
    protected $valid;
    protected $key;

    function __construct($connection, $stmt)
    {
        $this->connection = $connection;
        $this->stmt = $stmt;
    }

    function freeQuery()
    {
        if (isset($this->queryId) && $this->queryId > 0) {
            $this->connection->closeCursor($this->queryId);
            $this->queryId = null;
        }
    }

    function rewind(): void
    {
        if (isset($this->queryId) && $this->queryId > 0 && linter_get_cursor_opt($this->queryId, CO_ROW_COUNT)) {
            $ret = linter_fetch($this->queryId, 1, FETCH_ABSNUM);

            if ($ret < 0)
                $this->connection->_raiseError($ret, $this->queryId);
        } elseif (!$this->queryId) {

            if (is_array($this->sort_params))
                $this->stmt->addOrder($this->sort_params);

            if ($this->limit)
                $this->stmt->addLimit($this->offset, $this->limit);

            $this->queryId = $this->stmt->execute();
        }
        $this->key = 1;
        $this->next();
    }

    function next(): void
    {
        $this->current = new lmbLinterRecord();
        $values = linter_fetch_array($this->queryId);
        $this->current->import($values);
        $this->valid = is_array($values);
        $this->key++;
    }

    function valid(): bool
    {
        return $this->valid;
    }

    #[\ReturnTypeWillChange]
    function current()
    {
        return $this->current;
    }

    #[\ReturnTypeWillChange]
    function key()
    {
        return $this->key;
    }


    function at($pos)
    {
        $stmt = clone $this->stmt;
        if ($this->sort_params)
            $stmt->addOrder($this->sort_params);
        $stmt->addLimit($pos, 1);

        $queryId = $stmt->execute();
        $arr = linter_fetch_array($queryId);
        if ($this->queryId > 0)
            $this->connection->closeCursor($queryId);

        if (is_array($arr)) {
            $record = new lmbLinterRecord();
            $record->import($arr);
            return $record;
        }

    }

    function countPaginated()
    {
        if (is_null($this->queryId))
            $this->rewind();
        return linter_get_cursor_opt($this->queryId, CO_ROW_COUNT);
    }

    function count(): int
    {
        return $this->stmt->count();
    }
}
