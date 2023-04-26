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

  function __construct($config, $dsn_string = null)
  {
    $this->config = $config;
    if($config instanceof lmbDbDSN)
      $this->dsn_string = $config->toString();
    else
      $this->dsn_string = $dsn_string;
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

  function __sleep()
  {
    return array('config', 'dsn_string');
  }
}
