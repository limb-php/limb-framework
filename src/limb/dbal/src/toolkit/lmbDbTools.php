<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\toolkit;

use limb\config\src\toolkit\lmbConfTools;
use limb\dbal\src\drivers\lmbDbConnectionFactory;
use limb\dbal\src\drivers\lmbDbConnectionInterface;
use limb\toolkit\src\lmbAbstractTools;
use limb\core\src\lmbSet;
use limb\core\src\lmbEnv;
use limb\dbal\src\lmbDbDSN;
use limb\dbal\src\drivers\lmbDbCachedInfo;
use limb\dbal\src\lmbTableGateway;
use limb\core\src\exception\lmbException;

/**
 * class lmbDbTools.
 *
 * @package dbal
 * @version $Id: lmbDbTools.php 8007 2009-12-15 20:31:12Z
 */
class lmbDbTools extends lmbAbstractTools
{
    protected $dsnes_available = array('dsn' => null);
    protected $dsnes_active = array();
    protected $dsnes_names = array('dsn' => null);
    protected $db_info = array();
    protected $db_tables = array();
    protected $is_db_info_cache_enabled;
    protected $db_env = null;

    static function getRequiredTools()
    {
        return [
            lmbConfTools::class
        ];
    }

    function setDbEnvironment($env)
    {
        $this->dsnes_available = $env;
    }

    function getDbEnvironment()
    {
        return $this->dsnes_available;
    }

    function setDefaultDbDSN($dsn)
    {
        $this->setDbDSNByName('dsn', $dsn);
    }

    function getDefaultDbDSN()
    {
        return $this->getDbDSNByName('dsn');
    }

    function isDefaultDbDSNAvailable(): bool
    {
        try {
            $dsn = $this->getDefaultDbDSN();
            if ($dsn)
                return true;
            else
                return false;
        } catch (lmbException $e) {
            return false;
        }
    }

    function isDbDSNAvailable($dsn_name): bool
    {
        try {
            $dsn = $this->getDbDSNByName($dsn_name);
            if ($dsn)
                return true;
            else
                return false;
        } catch (lmbException $e) {
            return false;
        }
    }

    protected function _getDbDsnHash($dsn)
    {
        $dsn = self::castToDsnObject($dsn);
        return md5($dsn->toString());
    }

    static function castToDsnObject($dsn): lmbDbDSN
    {
        if ($dsn instanceof lmbDbDSN)
            return $dsn;
        if ($dsn instanceof lmbSet)
            return new lmbDbDSN($dsn->export());
        return new lmbDbDSN($dsn);
    }

    protected function _tryLoadDsnFromEnvironment($conf, $name)
    {
        if ($this->db_env) {
            $env = $conf->get($this->db_env);
            if (!is_array($env) || !isset($env[$name]))
                throw new lmbException("Could not find database connection settings for environment '{$this->db_env}'");

            return $env[$name];
        }

        return false;
    }

    protected function _loadDbDsnFromConfig($name)
    {
        $conf = $this->toolkit->getConf('db');

        /*TODO: remove BC */
        /*TODO: for BC 'dsn' overrides other db environments */
        $dsn = ($conf->has($name))
            ? $conf->get($name)
            : $this->_tryLoadDsnFromEnvironment($conf, $name);

        if (!$dsn)
            throw new lmbException("Could not find database connection settings '{$name}'", $conf);

        $dsn = self::castToDsnObject($dsn);
        $this->setDbDSNByName($name, $dsn);

        return $dsn->toString();
    }

    function setDbDSNByName($name, $dsn): void
    {
        $dsn = self::castToDsnObject($dsn);

        $this->dsnes_names[$name] = $this->_getDbDsnHash($dsn);
        $this->dsnes_available[$this->dsnes_names[$name]] = $dsn;
    }

    function getDbDSNByName($name)
    {
        if (!isset($this->dsnes_names[$name])) {
            $this->_loadDbDsnFromConfig($name);
        }

        if (isset($this->dsnes_available[$this->dsnes_names[$name]]) && is_object($this->dsnes_available[$this->dsnes_names[$name]]))
            return $this->dsnes_available[$this->dsnes_names[$name]];

        return $this->dsnes_available[$this->dsnes_names[$name]];
    }

    function getDbDSN($env): lmbDbDSN
    {
        $conf = $this->toolkit->getConf('db');
        $array = $conf->get($env);

        if (!is_array($array) || !isset($array['dsn']))
            throw new lmbException("Could not find database connection settings for environment '{$env}'");

        return new lmbDbDSN($array['dsn']);
    }

    function getDbConnectionByDsn($dsn): lmbDbConnectionInterface
    {
        $dsn_hash = $this->_getDbDsnHash($dsn);

        if (isset($this->dsnes_active[$dsn_hash]) && is_object($this->dsnes_active[$dsn_hash]))
            return $this->dsnes_active[$dsn_hash];

        $this->setDbConnectionByDsn($dsn, $this->createDbConnection($dsn));
        return $this->dsnes_active[$dsn_hash];
    }

    function setDbConnectionByDsn($dsn, $conn)
    {
        $this->dsnes_active[$this->_getDbDsnHash($dsn)] = $conn;
    }

    function setDbConnectionByName($name, $conn)
    {
        if (!is_object($dsn = $this->toolkit->getDbDSNByName($name)))
            throw new lmbException($name . ' database DSN is not valid');

        $this->setDbConnectionByDsn($dsn, $conn);
    }

    function getDbConnectionByName($name): lmbDbConnectionInterface
    {
        if (!is_object($dsn = $this->toolkit->getDbDSNByName($name)))
            throw new lmbException($name . ' database DSN is not valid');

        return $this->getDbConnectionByDsn($dsn);
    }

    function setDefaultDbConnection($conn)
    {
        $this->setDbConnectionByName('dsn', $conn);
    }

    function getDefaultDbConnection(): lmbDbConnectionInterface
    {
        return $this->getDbConnectionByName('dsn');
    }

    function createDbConnection($dsn): lmbDbConnectionInterface
    {
        $dsn = self::castToDsnObject($dsn);

        return (new lmbDbConnectionFactory)->make($dsn);
    }

    protected function _isDbInfoCacheEnabled()
    {
        if (is_null($this->is_db_info_cache_enabled)) {
            $this->is_db_info_cache_enabled = false;

            if (lmbEnv::has('LIMB_CACHE_DB_META_IN_FILE'))
                $this->is_db_info_cache_enabled = lmbEnv::get('LIMB_CACHE_DB_META_IN_FILE');
            else if ($this->toolkit->getConf('db')->has('cache_db_info'))
                $this->is_db_info_cache_enabled = $this->toolkit->getConf('db')->get('cache_db_info');
        }

        return $this->is_db_info_cache_enabled;
    }

    function getDbInfo(lmbDbConnectionInterface $conn)
    {
        $id = $conn->getHash();

        if (isset($this->db_info[$id]))
            return $this->db_info[$id];

        if ($this->_isDbInfoCacheEnabled())
            $db_info = new lmbDbCachedInfo($conn, lmbEnv::get('LIMB_VAR_DIR'));
        else
            $db_info = $conn->getDatabaseInfo();

        $this->db_info[$id] = $db_info;
        return $this->db_info[$id];
    }

    function createTableGateway($table_name, $conn = null)
    {
        if (!$conn)
            $cache_key = $table_name;
        else
            $cache_key = $table_name . $conn->getHash();

        if (isset($this->db_tables[$cache_key]))
            return $this->db_tables[$cache_key];

        $db_table = new lmbTableGateway($table_name, $conn);
        $this->db_tables[$cache_key] = $db_table;
        return $db_table;
    }
}
