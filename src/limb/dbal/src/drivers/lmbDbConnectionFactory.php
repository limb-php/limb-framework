<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers;

use limb\core\src\exception\lmbException;
use limb\dbal\src\drivers\mysql\lmbMysqlConnection;
use limb\dbal\src\drivers\pgsql\lmbPgsqlConnection;
use limb\dbal\src\drivers\sqlite\lmbSqliteConnection;
use limb\dbal\src\drivers\mssql\lmbMssqlConnection;
use limb\dbal\src\drivers\oci\lmbOciConnection;
use limb\dbal\src\drivers\linter\lmbLinterConnection;

/**
 * class lmbDbConnectionFactory.
 *
 * @package dbal
 * @version $Id$
 */
class lmbDbConnectionFactory
{
    static function make($dsn): lmbDbConnectionInterface
    {
        $driver = $dsn->get('driver');

        switch ($driver) {
            case 'mysql':
                return new lmbMysqlConnection($dsn);
            case 'pgsql':
                return new lmbPgsqlConnection($dsn);
            case 'sqlite':
                return new lmbSqliteConnection($dsn);
            case 'mssql':
                return new lmbMssqlConnection($dsn);
            case 'oci':
                return new lmbOciConnection($dsn);
            case 'linter':
                return new lmbLinterConnection($dsn);
            default:
                throw new lmbException("Driver '$driver' file not found for DSN '" . $dsn->toString() . "'!");
        }
    }
}
