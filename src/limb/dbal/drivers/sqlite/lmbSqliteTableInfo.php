<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\drivers\sqlite;

use limb\dbal\drivers\lmbDbTableInfo;
use limb\dbal\drivers\lmbDbIndexInfo;

/**
 * class lmbSqliteTableInfo.
 *
 * @package dbal
 * @version $Id$
 */
class lmbSqliteTableInfo extends lmbDbTableInfo
{
    protected $isExisting = false;
    protected $isColumnsLoaded = false;
    protected $isIndexesLoaded = false;
    protected $database;

    private $pk = array();

    function __construct($database, $name, $isExisting = false)
    {
        parent::__construct($name);
        $this->database = $database;
        $this->isExisting = $isExisting;
    }

    function getDatabase()
    {
        return $this->database;
    }

    //Based on code from Creole
    function loadColumns()
    {
        if ($this->isExisting && !$this->isColumnsLoaded) {
            $connection = $this->database->getConnection();
            $sql = "PRAGMA table_info('" . $this->name . "')";
            $rset = $connection->execute($sql);

            while ($row = $rset->fetchArray(SQLITE3_ASSOC)) {
                $name = $row['name'];

                $fulltype = $row['type'];
                $size = null;
                $scale = null;

                if (preg_match('/^([^\(]+)\(\s*(\d+)\s*,\s*(\d+)\s*\)$/', $fulltype, $matches)) {
                    $type = trim($matches[1]);
                    $size = $matches[2];
                    $scale = $matches[3]; // aka precision
                } elseif (preg_match('/^([^\(]+)\(\s*(\d+)\s*\)$/', $fulltype, $matches)) {
                    $type = trim($matches[1]);
                    $size = $matches[2];
                } else {
                    $type = $fulltype;
                }

                // If column is primary key and of type INTEGER, it is auto increment
                // See: http://sqlite.org/faq.html#q1
                $is_auto_increment = ($row['pk'] == 1 && $fulltype == 'INTEGER');
                $is_nullable = !$row['notnull'];

                $default_val = $row['dflt_value'];

                $this->columns[$name] = new lmbSqliteColumnInfo($this, $name, $type, $size, $scale,
                    $is_nullable, $default_val, $is_auto_increment);

                if (($row['pk'] == 1) || (strtolower($type) == 'integer primary key')) {
                    //primary key handling...
                    $this->pk[] = $name;
                }
            }

            $this->isColumnsLoaded = true;
        }
    }

    function loadIndexes()
    {
        if (!$this->isExisting || $this->isIndexesLoaded)
            return;

        $this->loadColumns();

        $connection = $this->database->getConnection();
        $rset = $connection->execute("PRAGMA index_list('$this->name')");

        while ($item = $rset->fetchArray(SQLITE3_ASSOC)) {
            $index = new lmbSqliteIndexInfo();
            $index->table = $this;
            $index->name = $item['name'];

            $rset2 = $connection->execute("PRAGMA index_info('$index->name')");

            $index_info = $rset2->fetchArray(SQLITE3_ASSOC);
            $index->column_name = $index_info['name'];

            if (1 == $item['unique']) {
                // if column is defined exactly as INTEGER PRIMARY KEY, primary index is not created
                // instead it becomes an alias of system ROWID, see http://www.sqlite.org/lang_createtable.html#rowid for more info
                $index->type = in_array($index['column_name'], $this->pk)
                    ? lmbDbIndexInfo::TYPE_PRIMARY : lmbDbIndexInfo::TYPE_UNIQUE;
            } else {
                $index->type = lmbDbIndexInfo::TYPE_COMMON;
            }

            $this->indexes[$index->name] = $index;
        }
        $this->isIndexesLoaded = true;
    }
}
