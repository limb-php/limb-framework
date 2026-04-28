<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\core\src;

use limb\core\src\exception\lmbNoSuchMethodException;
use limb\core\src\exception\lmbNoSuchPropertyException;

/**
 * Generic container for data with magic accessors.
 *
 * NOTE: All properties are stored as attributes of an object. If you need to
 * make certain properties 'guarded', you should prefix these properties
 * with "_" symbol.
 *
 * <b>Basic usage</b>
 * <code>
 * //filling object
 * $obj = new lmbObject(array('foo' => 'bar'));
 * //the getter calls below are equal
 * $obj->get('foo');
 * $obj->getFoo();
 * $obj['foo'];
 * $obj->foo;
 * //the setter calls below are equal
 * $obj->set('foo', 'hey');
 * $obj->setFoo('hey');
 * $obj['foo'] = 'hey';
 * $obj->foo = 'hey';
 * </code>
 *
 * <b>Mapping generic getters to fine-grained methods</b>
 * <code>
 * class Foo extends lmbObject
 * {
 *    function getBar()
 *    {
 *      return 'bar';
 *    }
 * }
 * $foo = new Foo();
 * //the call below will be magically mapped to getBar() method
 * //this can be very useful for templates, e.g. {$bar} in
 * //template will trigger the same method getBar() as well
 * $foo->get('bar');
 * </code>
 *
 * <b>Mapping generic setters to fine-grained methods</b>
 * <code>
 * class Foo extends lmbObject
 * {
 *    function setBar($value)
 *    {
 *      $this->_setRaw('bar', (int)$value);
 *    }
 * }
 * $foo = new Foo();
 * //the call below will be magically mapped to setBar($value) method
 * //this can be useful if some property processing is required
 * $foo->set('bar', '10.0');
 * </code>
 *
 * @version $Id: lmbObject.php 5567 2007-04-06 14:37:24Z
 * @package core
 */
class lmbObject implements lmbSetInterface
{
    protected $__properties = [];

    /**
     * Property-name -> getter-method lookup cache, keyed by class.
     *   self::$map_p2m[$class][$property] = $methodName | false
     *
     * Nested-by-class so callers can invalidate a single class cheaply
     * (e.g. between requests in a long-running worker) without wiping
     * the entries for every other class. A soft entry cap guards against
     * pathological growth when property names come from user input.
     */
    static $map_p2m = [];

    /** Running total of cached entries across all classes. */
    private static int $map_p2m_count = 0;

    /**
     * Maximum total cache entries before the cache is flushed.
     * 0 disables the cap entirely (legacy behaviour).
     */
    private static int $map_p2m_limit = 10000;

    private $_map = [
        'public' => [],
        'dynamic' => [],
        'initialized' => false,
    ];

    /**
     * Constructor.
     * Fills internals properties if any
     * @param array $properties
     */
    function __construct($properties = [])
    {
        $this->_registerPredefinedVariables();

        if ($properties)
            $this->import($properties);
    }

    protected function _registerPredefinedVariables()
    {
        if ($this->_map['initialized']) {
            return;
        }
        $var_names = get_object_vars($this);
        $var_names = array_merge_recursive($var_names, $this->__properties);
        foreach ($var_names as $key => $item) {
            if (!$this->_isGuarded($key))
                $this->_map['public'][$key] = $key;
        }

        $this->_map['initialized'] = true;
    }

    /**
     * Returns class name using PHP built in get_class
     * @return string
     * @see get_class
     */
    final function getClass()
    {
        return get_class($this);
    }

    /**
     * Merges existing properties with new ones
     * @param array $values
     */
    function import($values)
    {
        if (!is_array($values))
            return;

        foreach ($values as $property => $value)
            $this->_setRaw($property, $value);
    }

    /**
     * Exports all object properties as an array
     * @return array
     */
    public function export()
    {
        $exported = array();
        foreach ($this->getPropertiesNames() as $name)
            if(property_exists($this, $name))
                $exported[$name] = $this->$name;
            else
                $exported[$name] = $this->__properties[$name];

        return $exported;
    }

    /**
     * Checks if such property exists
     * Can be overridden in child classes like lmbActiveRecord
     * @return bool returns true even if attribute is null
     */
    function has($name): bool
    {
        return $this->_hasProperty($name) || $this->_mapPropertyToMethod($name);
    }

    protected function _hasProperty($name): bool
    {
        $this->_registerPredefinedVariables();
        return array_key_exists($name, $this->_map['public']);
    }

    function getPropertiesNames(): array
    {
        $this->_registerPredefinedVariables();
        return array_keys($this->_map['public']);
    }

    /**
     * Alias for getPropertiesNames
     *
     * @deprecated
     */
    function getAttributesNames()
    {
        return $this->getPropertiesNames();
    }

    /**
     * Removes specified property
     * @param string $name
     */
    function remove($name)
    {
        if ($this->_isGuarded($name))
            return;

        unset($this->_map['public'][$name]);
        unset($this->_map['dynamic'][$name]);
        if(property_exists($this, $name))
            unset($this->$name);
        else
            unset($this->__properties[$name]);
    }

    /**
     * Removes all object properties
     */
    function reset()
    {
        $this->_map['public'] = array();
        $this->_map['dynamic'] = array();
        foreach ($this->getPropertiesNames() as $name) {
            if(property_exists($this, $name))
                unset($this->$name);
            else
                unset($this->__properties[$name]);
        }
    }

    /**
     * Returns property value if it exists and not guarded.
     * Magically maps getter to fine-grained method if it exists, e.g. get('foo') => getFoo()
     * @param string $name property name
     * @param mixed $default default value
     * @return mixed|null
     */
    function get($name, $default = null)
    {
        if ($method = $this->_mapPropertyToMethod($name))
            return $this->$method();

        if ($this->_hasProperty($name))
            return $this->_getRaw($name);

        #TODO ???
        if (null !== $default)
            return $default;

        throw new lmbNoSuchPropertyException("No such property '$name' in " . get_class($this));
    }

    /**
     * Sets property value
     * Magically maps setter to fine-grained method if it exists, e.g. set('foo', $value) => setFoo($value)
     * @param string $name property name
     * @param mixed $value
     * @return $this
     */
    function set($name, $value)
    {
        if ($name) {
            if ($method = $this->_mapPropertyToSetMethod($name)) {
                $this->$method($value);
            } else {
                $this->_setRaw($name, $value);
            }
        }

        return $this;
    }

    protected function _getRaw($name)
    {
        if ($this->_hasProperty($name)) {
            if(property_exists($this, $name))
                return $this->$name ?? null;
            else
                return $this->__properties[$name] ?? null;
        }
    }

    protected function _setRaw($name, $value)
    {
        if ($this->_isGuarded($name))
            return;

        $this->_map['public'][$name] = $name;
        $this->_map['dynamic'][$name] = $name;

        if(property_exists($this, $name))
            $this->$name = $value;
        else
            $this->__properties[$name] = $value;
    }

    protected function _isGuarded($property)
    {
        return isset($property[0]) && $property[0] == '_';
    }

    /**#@+
     * Implements ArrayAccess interface
     * @see ArrayAccess
     */
    function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    #[\ReturnTypeWillChange]
    function offsetGet($offset)
    {
        return $this->get($offset);
    }

    function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    /**#@-*/

    function __call($method, $args = array())
    {
        if ($property = $this->_mapGetToProperty($method)) {
            if ($this->has($property))
                return $this->get($property);
        } elseif ($property = $this->_mapSetToProperty($method)) {
            $this->set($property, $args[0]);
            return $this;
        }

        throw new lmbNoSuchMethodException("No such method '$method()' in " . get_class($this));
    }

    protected function _mapGetToProperty($method)
    {
        if (0 === strpos($method, 'get'))
            return lmbString::under_scores(substr($method, 3));
    }

    protected function _mapSetToProperty($method)
    {
        if (0 === strpos($method, 'set'))
            return lmbString::under_scores(substr($method, 3));
    }

    protected function _mapPropertyToMethod($property)
    {
        $class = static::class;

        if (isset(self::$map_p2m[$class]) && array_key_exists($property, self::$map_p2m[$class]))
            return self::$map_p2m[$class][$property];

        $capsed = lmbString::camel_case($property);
        $method = 'get' . $capsed;
        if ($method !== 'get' && method_exists($this, $method))
            return self::_rememberP2M($class, $property, $method);

        //'is_foo' property is mapped to 'isFoo' method if it exists
        if (strpos($property, 'is_') === 0 && method_exists($this, $capsed))
            return self::_rememberP2M($class, $property, $capsed);

        return self::_rememberP2M($class, $property, false);
    }

    /**
     * Store a property->method mapping and enforce the soft cap.
     * When the cap is exceeded we clear the whole cache rather than
     * running an LRU — callers that care about long-running workers
     * should call clearP2MCache() periodically on their own cadence.
     *
     * @param string       $class
     * @param string       $property
     * @param string|false $method
     * @return string|false
     */
    private static function _rememberP2M(string $class, string $property, $method)
    {
        if (self::$map_p2m_limit > 0 && self::$map_p2m_count >= self::$map_p2m_limit)
            self::clearP2MCache();

        if (!isset(self::$map_p2m[$class][$property]))
            self::$map_p2m_count++;

        self::$map_p2m[$class][$property] = $method;
        return $method;
    }

    /**
     * Flush the property->method resolution cache.
     *
     * @param string|null $class When null (default), clears everything.
     *                           Otherwise clears only entries for that class.
     */
    public static function clearP2MCache(?string $class = null): void
    {
        if ($class === null) {
            self::$map_p2m = [];
            self::$map_p2m_count = 0;
            return;
        }
        if (isset(self::$map_p2m[$class])) {
            self::$map_p2m_count -= count(self::$map_p2m[$class]);
            if (self::$map_p2m_count < 0)
                self::$map_p2m_count = 0;
            unset(self::$map_p2m[$class]);
        }
    }

    /**
     * Configure the soft cap on total cache entries.
     * Pass 0 to disable the cap (legacy behaviour).
     */
    public static function setP2MCacheLimit(int $limit): void
    {
        self::$map_p2m_limit = max(0, $limit);
    }

    /**
     * Introspection helper for daemons / monitoring. Returns:
     *   ['entries' => int, 'classes' => int, 'limit' => int]
     */
    public static function getP2MCacheStats(): array
    {
        return [
            'entries' => self::$map_p2m_count,
            'classes' => count(self::$map_p2m),
            'limit' => self::$map_p2m_limit,
        ];
    }

    protected function _mapPropertyToSetMethod($property)
    {
        $method = 'set' . lmbString::camel_case($property);
        if ($method !== 'set' && method_exists($this, $method))
            return $method;
    }

    /**
     * __set  an alias of set()
     * @see set, offsetSet
     */
    function __set($property, $value)
    {
        if (array_key_exists($property, $this->_map['dynamic'])) {
            if(property_exists($this, $property))
                $this->$property = $value;
            else
                $this->__properties[$property] = $value;
        }
        else {
            $this->set($property, $value);
        }
    }

    /**
     * __get -- an alias of get()
     * @return mixed
     * @see get,  offsetGet
     */
    function __get($property)
    {
        return $this->get($property);
    }

    /**
     * __isset  an alias of has()
     * @return boolean whether or not this object contains $name
     */
    function __isset($name)
    {
        return $this->has($name);
    }

    /**
     * __unser  an alias of remove()
     * @param string $name
     */
    function __unset($name)
    {
        $this->remove($name);
    }

    #[\ReturnTypeWillChange]
    function current()
    {
        return $this->_getRaw($this->key());
    }

    function next(): void
    {
        next($this->_map['public']);
    }

    #[\ReturnTypeWillChange]
    function key()
    {
        return current($this->_map['public']);
    }

    function valid(): bool
    {
        return (bool)$this->key();
    }

    function rewind(): void
    {
        reset($this->_map['public']);
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        $exported = [];
        $properties = $this->getPropertiesNames();
        foreach ($properties as $name)
            $exported[$name] = $this->get($name);

        return $exported;
    }
}
