<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\drivers;

use limb\core\exception\lmbException;
use limb\dbal\drivers\mysql\lmbMysqlConnection;
use limb\dbal\drivers\pgsql\lmbPgsqlConnection;
use limb\dbal\drivers\sqlite\lmbSqliteConnection;
use limb\dbal\drivers\mssql\lmbMssqlConnection;
use limb\dbal\drivers\oci\lmbOciConnection;
use limb\dbal\drivers\linter\lmbLinterConnection;

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
