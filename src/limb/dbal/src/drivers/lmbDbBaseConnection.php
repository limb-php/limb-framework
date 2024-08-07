<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers;

use limb\dbal\src\lmbDbDSN;
use limb\dbal\src\query\lmbQueryLexerInterface;
use limb\toolkit\src\lmbToolkit;
use Psr\Log\LoggerInterface;

/**
 * class lmbBaseDbConnection.
 * A base class for all connection classes
 *
 * @package dbal
 * @version $Id$
 */
abstract class lmbDbBaseConnection implements lmbDbConnectionInterface
{
    protected $config;
    protected $dsn_string;
    protected $extension;
    /** @var $logger LoggerInterface */
    protected $logger;

    protected $queryLog = [];

    function __construct($config)
    {
        $this->config = $config;
        if ($config instanceof lmbDbDSN)
            $this->dsn_string = $config->toString();
        elseif(is_string($config))
            $this->dsn_string = $config;

        $this->logger = lmbToolkit::instance()->getLog('db');
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    function getConfig()
    {
        return $this->config;
    }

    function getHash()
    {
        return crc32(serialize($this->config));
    }

    abstract function getExtension();

    abstract function getLexer(): lmbQueryLexerInterface;

    function transaction(\Closure $callback)
    {
        $this->beginTransaction();

        try {
            $result = $callback($this);

            $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        } catch (\Throwable $e) {
            $this->rollbackTransaction();
            throw $e;
        }

        return $result;
    }

    function getDsnString()
    {
        return $this->dsn_string;
    }

    function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    function withLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    abstract function executeSQL($sql, $retry = true);
    abstract function executeSQLStatement(lmbDbStatementInterface $stmt, $retry = true);

    function execute($sql, $retry = true)
    {
        $info = array('query' => $sql);
        $start_time = microtime(true);

        $res = $this->executeSQL($sql, $retry);

        $info['time'] = round(microtime(true) - $start_time, 6);
        $this->queryLog[] = $info;

        if($this->logger)
            $this->logger->debug($this->getType() . " Driver. Execute SQL: " . implode(" ", $info) . "\n");

        return $res;
    }

    function executeStatement(lmbDbStatementInterface $stmt, $retry = true)
    {
        $info = array('query' => $stmt->getSQL());
        $info['params'] = var_export($stmt->getPrepParams(), true);
        $start_time = microtime(true);

        $res = $this->executeSQLStatement($stmt, $retry);

        $info['time'] = round(microtime(true) - $start_time, 6);
        $this->queryLog[] = $info;

        if($this->logger)
            $this->logger->debug($this->getType() . " Driver. Execute statement: " . implode(" ", $info) . "\n");

        return $res;
    }

    function countQueries()
    {
        return sizeof($this->queryLog);
    }

    function resetStats()
    {
        $this->queryLog = [];
    }

    function getQueryLog()
    {
        return $this->queryLog;
    }

    function getQueries($reg_exp = '')
    {
        $res = array();
        foreach ($this->queryLog as $info) {
            $query = $info['query'];
            if (!$reg_exp || preg_match('/' . $reg_exp . '/i', $query))
                $res[] = $query;
        }

        return $res;
    }

    function __sleep()
    {
        return array('config', 'dsn_string');
    }
}
