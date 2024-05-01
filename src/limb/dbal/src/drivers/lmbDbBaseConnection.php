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

    function __sleep()
    {
        return array('config', 'dsn_string');
    }
}
