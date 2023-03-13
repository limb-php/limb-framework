<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\toolkit\src;

use limb\core\src\exception\lmbException;
use limb\core\src\exception\lmbNoSuchPropertyException;
use limb\core\src\lmbObject;
use limb\core\src\lmbString;
use limb\core\src\exception\lmbNoSuchMethodException;

/**
 * Toolkit is an implementation of Dinamic Service Locator pattern
 * The idea behind lmbToolkit class is simple:
 *  1) lmbToolkit is a Singleton
 *  2) lmbToolkit consists of so-called tools. Tools is an object of any class that supports {@link lmbToolkitToolsInterface} interface
 *  3) lmbToolkit redirects all non-existing methods via magic __call to tools if these methods were named in $tools :: getToolsSignatures() result.
 *  4) lmbToolkit also acts as a registry. You can put any data into toolkit and get them out at any place of your application
 * As a result we get an easily accessible object that we can extend with any methods we need.
 * We can also replace one tools with others thus we can return to client code completely different results from the same toolkit methods.
 * lmbToolkit also supports magic getters and setters. Say you have tools with getVar() method and you call $toolkit->get('var') then tools->getVar() will be actually called
 * Example of usage:
 * <code>
 * lmbToolkit::merge(new limb\net\src\lmbNetTools());
 * lmbToolkit::merge(new limb\net\src\toolkit\lmbDbTools());
 * // somewhere in client code
 * $toolkit = lmbToolkit::instance();
 * $toolkit->set('my_var', $value)'
 * $request = $toolkit->getRequest(); // supported by lmbNetTools
 * $same_request = $toolkit->get('request'); // will delegate to getRequest()
 * $db_connection = $toolkit->getDefaultDbConnection(); // supported by lmbDbTools
 * $toolkit->get('my_var'); // returns $value value
 * </code>
 * @see lmbToolkitToolsInterface
 * @package toolkit
 * @version $Id: lmbToolkit.php 8177 2010-04-23 18:10:17Z
 *
 * @see lmbFsTools
 * @method getAcl()
 * @method setAcl($acl)
 *
 * @see lmbARTools
 * @method getActiveRecordMetaInfo($active_record, $conn = null)
 *
 * @see lmbCacheTools
 * @method getCache()
 * @method setCache($cache)
 *
 * @see lmbCmsTools
 * @method getCmsTree($tree_name = 'node')
 * @method setCmsTree($tree)
 * @method getCmsUser()
 * @method resetCmsUser()
 * @method setCmsUser($user)
 *
 * @see lmbConfTools
 * @method setConf($name, $conf)
 * @method hasConf($name)
 * @method setConfIncludePath($path)
 * @method getConfIncludePath()
 * @method getConf($name)
 * @method parseYamlFile($file)
 *
 * @see lmbDbTools
 * @method setDbEnvironment($env)
 * @method getDbEnvironment()
 * @method getDefaultDbDSN()
 * @method isDefaultDbDSNAvailable()
 * @method castToDsnObject($dsn)
 * @method setDbDSNByName($name, $dsn)
 * @method getDbDSNByName($name)
 * @method getDbDSN($env)
 * @method getDbConnectionByDsn($dsn)
 * @method setDbConnectionByDsn($dsn, $conn)
 * @method setDbConnectionByName($name, $conn)
 * @method getDefaultDbConnection()
 * @method getDbConnectionByName($name)
 * @method setDefaultDbConnection($conn)
 * @method createDbConnection($dsn)
 * @method getDbInfo($conn)
 * @method createTableGateway($table_name, $conn = null)
 *
 * @see lmbFsTools
 * @method findFileByAlias($alias, $paths, $locator_name = null, $find_all = false)
 * @method tryFindFileByAlias($alias, $paths, $locator_name = null)
 * @method getFileLocator($paths, $locator_name = null)
 *
 * @see lmbI18NTools
 * @method getDictionaryBackend()
 * @method setDictionaryBackend($backend)
 * @method getLocale()
 * @method setLocale($locale)
 * @method getLocaleObject($locale = null)
 * @method addLocaleObject($obj, $locale = null)
 * @method createLocaleObject($locale)
 * @method getDictionary($locale, $domain)
 * @method setDictionary($locale, $domain, $dict)
 * @method translate($text, $arg1 = null, $arg2 = null)
 *
 * @see \limb\log\src\toolkit\lmbLogTools
 * @method getLogDSNes()
 * @method getLog()
 * @method setLog($log)
 *
 * @see lmbMailTools
 * @method getMailer()
 *
 * @see lmbNetTools
 * @method getRequest()
 * @method setRequest($request)
 * @method getResponse()
 * @method setResponse($response)
 *
 *
 */
class lmbToolkit extends lmbObject
{
  /**
  * @var lmbToolkit Toolkit singleton instance
  */
  static protected $_instance = null;
  /**
  * @var array Current tools array
  */
  protected $_tools = array();
  /**
  * @var array Cached tools signatures that is methods supported by tools
  */
  protected $_tools_signatures = array();
  /**
  * @var boolean Flag if tools signatures were precached
  */
  protected $_signatures_loaded = false;
  /**
  * @var string Unique id of this toolkit
  */
  protected $_id;

  function __construct()
  {
    $this->_id = uniqid();
  }

  /**
  * Follows Singleton pattern interface
  * Returns toolkit instance. Takes instance from {@link lmbRegistry)
  * If instance is not initialized yet - creates one with empty tools
  * @see lmbRegistry
  * @return lmbToolkit The only instance of lmbToolkit class
  */
  static function instance()
  {
    if(is_object(self::$_instance))
      return self::$_instance;

    self::$_instance = new lmbToolkit();
    return self::$_instance;
  }

  /**
  * Sets new tools object and clear signatures cache
  * @param $tools lmbToolkitToolsInterface|array
  */
  protected function setTools($tools)
  {
    if(!is_array($tools))
      $this->_tools = array($tools);
    else
      $this->_tools = $tools;

    $this->_tools_signatures = array();
    $this->_signatures_loaded = false;
  }

  /**
  * Fills toolkit instance with suggested tools and registers this tools in {@ling lmbRegistry}
  * @see lmbRegistry
  * @return lmbToolkit The only instance of lmbToolkit class
  */
  static function setup($tools): self
  {
    $toolkit = lmbToolkit::instance();
    $toolkit->setTools($tools);

    return $toolkit;
  }

    /**
     * Save current tools object in registry stack and creates a new one using currently saved empty copy of tools object
     * @return lmbToolkit The only instance of lmbToolkit class
     * @throws lmbException
     * @see lmbRegistry::save()
     */
  static function save(): self
  {
    $toolkit = lmbToolkit::instance();

    $tools = $toolkit->_tools;
    $tools_copy = array();
    foreach($toolkit->_tools as $tool)
      $tools_copy[] = clone($tool);

    lmbRegistry::set('__tools' . $toolkit->_id, $tools);
    lmbRegistry::save('__tools' . $toolkit->_id);
    $toolkit->setTools($tools_copy);

    lmbRegistry::set('__props' . $toolkit->_id, $toolkit->export());
    lmbRegistry::save('__props' . $toolkit->_id);

    return $toolkit;
  }

    /**
     * Restores previously saved tools object instance from {@link lmbRegistry} stack and sets this tools into toolkit instance
     * @return lmbToolkit The only instance of lmbToolkit class
     * @throws lmbException
     */
  static function restore(): self
  {
    $toolkit = lmbToolkit::instance();

    lmbRegistry::restore('__tools' . $toolkit->_id);
    $tools = lmbRegistry::get('__tools' . $toolkit->_id);
    lmbRegistry::restore('__props' . $toolkit->_id);
    $props = lmbRegistry::get('__props' . $toolkit->_id);

    if($props !== null)
    {
      $toolkit->reset();
      $toolkit->import($props);
    }

    if($tools !== null)
      $toolkit->setTools($tools);

    return $toolkit;
  }

  /**
  * Extends current tools with new tool
  * @return lmbToolkit The only instance of lmbToolkit class
  */
  static function merge($tool, $name = ''): self
  {
    $toolkit = lmbToolkit::instance();
    $toolkit->add($tool, $name);
    return $toolkit;
  }

  /**
  * Extends current tools with new tool
  */
  function add($tool, $name = '')
  {
    if( !$name )
      $name = get_class($tool);

    if( !isset($this->_tools[$name]) )
    {
      $req_tools = $tool::getRequiredTools();
      if (!empty($req_tools)) {
        foreach ($req_tools as $req_tool) {
          lmbToolkit::merge( new $req_tool() );
        }
      }

      if( method_exists($tool, '_init') )
        call_user_func_array(array($tool, '_init'), array());

      $tools = $this->_tools;
      $tools = array($name => $tool) + $tools;
      $this->setTools($tools);
    }
  }

  /**
  * Sets variable into toolkit
  * Checks if appropriate setter method in tools exists to delegate to
  * @return void
  */
  function set($name, $value)
  {
    if($method = $this->_mapPropertyToSetMethod($name))
      $this->$method($value);
    else
      parent::set($name, $value);
  }

    /**
     * Gets variable from toolkit
     * Checks if appropriate getter method in tools exists to delegate to
     * @return mixed
     * @throws lmbNoSuchPropertyException
     */
  function get($name, $default = null)
  {
    if($method = $this->_mapPropertyToGetMethod($name))
      return $this->$method();
    else
      return parent::get($name, $default);
  }

  function has($name): bool
  {
    return $this->_hasGetMethodFor($name) || parent::has($name);
  }

  /**
  * Sets variable into toolkit directly
  * @return void
  */
  function setRaw($var, $value)
  {
    parent::_setRaw($var, $value);
  }

  /**
  * Gets variable from toolkit directly
  * @return mixed
  */
  function getRaw($var)
  {
    return parent::_getRaw($var);
  }

    /**
     * Magic caller. Delegates to {@link $tools} if $tools_signatures has required method
     * @param string $method Method name
     * @param array $args Method arguments
     * @return mixed
     * @throws lmbNoSuchMethodException
     */
  public function __call($method, $args = array())
  {
    $this->_ensureSignatures();

    if(isset($this->_tools_signatures[$method]))
      return call_user_func_array(array($this->_tools_signatures[$method], $method), $args);

    throw new lmbNoSuchMethodException("No such method '$method' exists in toolkit");
  }

  /**
  * Caches tools signatures. Fills {@link $tools_signatures}.
  * @see lmbToolkitToolsInterface::getToolsSignatures()
  * @return void
  */
  protected function _ensureSignatures()
  {
    if($this->_signatures_loaded)
      return;

    $this->_tools_signatures = array();
    foreach($this->_tools as $tool)
    {
      $signatures = $tool->getToolsSignatures();
      foreach($signatures as $method => $obj)
      {
        if(!isset($this->_tools_signatures[$method]))
          $this->_tools_signatures[$method] = $obj;
      }
    }

    $this->_signatures_loaded = true;
  }

  protected function _hasGetMethodFor($property): bool
  {
    return (bool) $this->_mapPropertyToGetMethod($property);
  }

  protected function _mapPropertyToGetMethod($property)
  {
    $this->_ensureSignatures();

    $capsed = lmbString::camel_case($property);
    $method = 'get' . $capsed;
    if(isset($this->_tools_signatures[$method]))
      return $method;

    return false;
  }

  protected function _mapPropertyToSetMethod($property)
  {
    $this->_ensureSignatures();

    $method = 'set' . lmbString::camel_case($property);
    if(isset($this->_tools_signatures[$method]))
      return $method;

    return false;
  }
}
