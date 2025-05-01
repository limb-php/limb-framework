<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\Toolkit;

use Limb\Core\Exception\lmbException;
use Limb\Core\Exception\lmbNoSuchPropertyException;
use Limb\Core\lmbObject;
use Limb\Core\lmbString;
use Limb\Core\Exception\lmbNoSuchMethodException;
use Psr\Http\Message\ResponseInterface;

/**
 * Toolkit is an implementation of Dinamic Service Locator pattern
 * The idea behind lmbToolkit class is simple:
 *  1) lmbToolkit is a Singleton
 *  2) lmbToolkit consists of so-called tools. Tools is an object of any class that supports {@link lmbToolkitToolsInterface} interface
 *  3) lmbToolkit redirects all non-existing methods via magic __call to tools if these methods were named in $tools::getToolsSignatures() result.
 *  4) lmbToolkit also acts as a registry. You can put any data into toolkit and get them out at any place of your application
 * As a result we get an easily accessible object that we can extend with any methods we need.
 * We can also replace one tools with others thus we can return to client code completely different results from the same toolkit methods.
 * lmbToolkit also supports magic getters and setters. Say you have tools with getVar() method and you call $toolkit->get('var') then tools->getVar() will be actually called
 * Example of usage:
 * <code>
 * lmbToolkit::merge(new limb\net\lmbNetTools());
 * lmbToolkit::merge(new limb\net\toolkit\lmbDbTools());
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
 * @method \limb\acl\lmbAcl getAcl()
 * @method void setAcl($acl)
 *
 * @see lmbARTools
 * @method getActiveRecordMetaInfo($table_name, \limb\dbal\drivers\lmbDbConnectionInterface $conn)
 * @method getActiveRecordMetaInfoByAR($active_record, \limb\dbal\drivers\lmbDbConnectionInterface $conn = null)
 *
 * @see lmbCacheTools
 * @method \limb\cache\lmbCacheBackendInterface|null getCache()
 * @method setCache($cache)
 *
 * @see lmbCmsTools
 * @method \limb\tree\lmbMPTree getCmsTree($tree_name = 'node')
 * @method void setCmsTree(\limb\tree\lmbMPTree $tree)
 * @method string getUserSessionName()
 * @method \limb\cms\model\lmbCmsSessionUser getCmsAuthSession()
 * @method \limb\cms\model\lmbCmsUser|\limb\acl\lmbRoleProviderInterface getCmsUser()
 * @method void setCmsUser($user)
 * @method void resetCmsUser()
 *
 * @see lmbConfTools
 * @method \limb\core\lmbSetInterface getConf($name)
 * @method \limb\core\lmbObject parseYamlFile($file)
 * @method void setConf($name, $conf)
 * @method bool hasConf($name)
 * @method void setConfIncludePath($path)
 * @method getConfIncludePath()
 *
 * @see lmbDbTools
 * @method setDbEnvironment($env)
 * @method getDbEnvironment()
 * @method getDefaultDbDSN()
 * @method setDefaultDbDSN($dsn)
 * @method isDefaultDbDSNAvailable()
 * @method castToDsnObject($dsn)
 * @method setDbDSNByName($name, $dsn)
 * @method getDbDSNByName($name)
 * @method getDbDSN($env)
 * @method \limb\dbal\drivers\lmbDbConnectionInterface getDbConnectionByDsn($dsn)
 * @method setDbConnectionByDsn($dsn, $conn)
 * @method setDbConnectionByName($name, $conn)
 * @method \limb\dbal\drivers\lmbDbConnectionInterface getDefaultDbConnection()
 * @method \limb\dbal\drivers\lmbDbConnectionInterface getDbConnectionByName($name)
 * @method setDefaultDbConnection(\limb\dbal\drivers\lmbDbConnectionInterface $conn)
 * @method \limb\dbal\drivers\lmbDbConnectionInterface createDbConnection($dsn)
 * @method getDbInfo(\limb\dbal\drivers\lmbDbConnectionInterface $conn)
 * @method \limb\dbal\lmbTableGateway createTableGateway($table_name, $conn = null)
 *
 * @see lmbFsTools
 * @method findFileByAlias($alias, $paths, $locator_name = null, $find_all = false)
 * @method tryFindFileByAlias($alias, $paths, $locator_name = null)
 * @method \limb\fs\lmbFileLocator getFileLocator($paths, $locator_name = null)
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
 * @see lmbLogTools
 * @method getLogConfs(): array
 * @method string getDefaultErrorConf()
 * @method \Psr\Log\LoggerInterface getLog($name = 'error')
 * @method setLog($name, \Psr\Log\LoggerInterface $log)
 *
 * @see lmbMailTools
 * @method getMailer()
 *
 * @see lmbNetTools
 * @method \Psr\Http\Message\RequestInterface getRequest()
 * @method setRequest(\Psr\Http\Message\RequestInterface $request)
 * @method \Psr\Http\Message\ResponseInterface getResponse($content = '', $status = 200, $headers = [])
 * @method setResponse(\Psr\Http\Message\ResponseInterface $response)
 *
 * @see lmbSessionTools
 * @method \limb\session\lmbSession getSession()
 * @method setSession(\limb\session\lmbSession $session)
 *
 * @see lmbViewTools
 * @method setSupportedViewTypes($types)
 * @method getSupportedViewTypes()
 * @method getSupportedViewExtensions()
 * @method locateTemplateByAlias($alias, $view_class = null)
 * @method \limb\view\lmbViewInterface createViewByTemplate($template_name, $vars = [])
 * @method getMacroConfig()
 * @method getMacroLocator()
 * @method setMacroConfig($config)
 * @method getTwigConfig()
 * @method setTwigConfig($config)
 *
 * @see lmbWebAppTools
 * @method setView(\limb\view\lmbViewInterface|null $view)
 * @method \limb\view\lmbViewInterface|null getView()
 * @method setDispatchedController($dispatched)
 * @method \limb\web_app\src\Controllers\lmbController getDispatchedController()
 * @method \limb\web_app\src\Controllers\lmbController createController($controller_name, $namespace = '') {
 *      @throws \limb\web_app\src\exception\lmbControllerNotFoundException
 * }
 * @method getRouteUrlByName($route_name, $params = array())
 * @method string getRoutesUrl($params = array(), $route_name = '', $skip_controller = false)
 * @method \limb\web_app\src\request\lmbRoutes getRoutes()
 * @method setRoutes($routes)
 * @method \limb\web_app\src\util\lmbFlashBox getFlashBox()
 * @method flashError($message)
 * @method flashMessage($message)
 * @method ResponseInterface redirectToRoute(array $params, string $route_name = '', $append = '')
 * @method ResponseInterface redirect($params_or_url = [], $route_url = null, $append = '')
 * @method bool isWebAppDebugEnabled()
 * @method addVersionToUrl($file_src, $safe = false)
 * @method getNormalizeUrlAndVersion($file_src, $safe = false)
 * @method selectDomainForFile($domains, $file_src, $safe = false)
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
     * @return lmbToolkit The only instance of lmbToolkit class
     * @see lmbRegistry
     */
    static function instance(): lmbToolkit
    {
        if (is_object(self::$_instance))
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
        if (!is_array($tools))
            $this->_tools = array($tools);
        else
            $this->_tools = $tools;

        $this->_tools_signatures = array();
        $this->_signatures_loaded = false;
    }

    /**
     * Fills toolkit instance with suggested tools and registers this tools in {@ling lmbRegistry}
     * @return lmbToolkit The only instance of lmbToolkit class
     * @see lmbRegistry
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
        foreach ($toolkit->_tools as $tool)
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

        if ($props !== null) {
            $toolkit->reset();
            $toolkit->import($props);
        }

        if ($tools !== null)
            $toolkit->setTools($tools);

        return $toolkit;
    }

    /**
     * Extends current tools with new tool
     * @return lmbToolkit The only instance of lmbToolkit class
     */
    static function merge(lmbToolkitToolsInterface $tool, $name = ''): self
    {
        $toolkit = lmbToolkit::instance();
        $toolkit->add($tool, $name);
        return $toolkit;
    }

    /**
     * Extends current tools with new tool
     */
    function add(lmbToolkitToolsInterface $tool, $name = '')
    {
        if (!$name)
            $name = get_class($tool);

        if (!isset($this->_tools[$name])) {
            $req_tools = $tool::getRequiredTools();
            if (!empty($req_tools)) {
                foreach ($req_tools as $req_tool_class) {
                    lmbToolkit::merge(new $req_tool_class());
                }
            }

            if (method_exists($tool, 'bootstrap'))
                call_user_func_array(array($tool, 'bootstrap'), array());
            //$tool->bootstrap();

            $tools = $this->_tools;
            $tools = array($name => $tool) + $tools;
            $this->setTools($tools);
        }
    }

    /**
     * Sets variable into toolkit
     * Checks if appropriate setter method in tools exists to delegate to
     * @return static
     */
    function set($name, $value): static
    {
        if ($method = $this->_mapPropertyToSetMethod($name))
            $this->$method($value);
        else
            parent::set($name, $value);

        return $this;
    }

    /**
     * Gets variable from toolkit
     * Checks if appropriate getter method in tools exists to delegate to
     * @return mixed
     * @throws lmbNoSuchPropertyException
     */
    function get($name, $default = null)
    {
        if ($method = $this->_mapPropertyToGetMethod($name))
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

        if (isset($this->_tools_signatures[$method]))
            return call_user_func_array(array($this->_tools_signatures[$method], $method), $args);

        throw new lmbNoSuchMethodException("No such method '$method' exists in toolkit");
    }

    /**
     * Caches tools signatures. Fills {@link $tools_signatures}.
     * @return void
     * @see lmbToolkitToolsInterface::getToolsSignatures()
     */
    protected function _ensureSignatures()
    {
        if ($this->_signatures_loaded)
            return;

        $this->_tools_signatures = array();
        foreach ($this->_tools as $tool) {
            $signatures = $tool->getToolsSignatures();
            foreach ($signatures as $method => $obj) {
                if (!isset($this->_tools_signatures[$method]))
                    $this->_tools_signatures[$method] = $obj;
            }
        }

        $this->_signatures_loaded = true;
    }

    protected function _hasGetMethodFor($property): bool
    {
        return (bool)$this->_mapPropertyToGetMethod($property);
    }

    protected function _mapPropertyToGetMethod($property)
    {
        $this->_ensureSignatures();

        $capsed = lmbString::camel_case($property);
        $method = 'get' . $capsed;
        if (isset($this->_tools_signatures[$method]))
            return $method;

        return false;
    }

    protected function _mapPropertyToSetMethod($property)
    {
        $this->_ensureSignatures();

        $method = 'set' . lmbString::camel_case($property);
        if (isset($this->_tools_signatures[$method]))
            return $method;

        return false;
    }
}
