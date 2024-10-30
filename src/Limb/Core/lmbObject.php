<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\Core;

use Limb\Core\Exception\lmbNoSuchMethodException;
use Limb\Core\Exception\lmbNoSuchPropertyException;

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
 * @version $Id: lmbObject.php
 * @package core
 */
class lmbObject implements lmbSetInterface
{
    protected array $__properties = [];

    static protected $map_p2m = [];

    private $_map = [
        'public' => [],
        'dynamic' => [],
        'initialized' => false,
    ];

    /**
     * Fills internals properties if any
     * @param array $properties
     */
    function __construct(array $properties = [])
    {
        $this->registerPredefinedVariables();

        if ($properties)
            $this->import($properties);
    }

    protected function registerPredefinedVariables()
    {
        if ($this->_map['initialized']) {
            return;
        }
        $var_names = get_object_vars($this);
        $var_names = array_merge_recursive($var_names, $this->__properties);
        foreach ($var_names as $key => $item) {
            if (!$this->isGuarded($key))
                $this->_map['public'][$key] = $key;
        }

        $this->_map['initialized'] = true;
    }

    /**
     * Returns class name using PHP built in get_class
     * @return string
     * @see get_class
     */
    final public function getClass()
    {
        return get_class($this);
    }

    /**
     * Merges existing properties with new ones
     * @param array $values
     */
    public function import($values): void
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
    public function export(): array
    {
        $exported = [];
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
        return $this->hasProperty($name) || $this->_mapPropertyToMethod($name);
    }

    protected function hasProperty($name): bool
    {
        $this->registerPredefinedVariables();
        return array_key_exists($name, $this->_map['public']);
    }

    public function getPropertiesNames(): array
    {
        $this->registerPredefinedVariables();
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
    function remove($name): void
    {
        if ($this->isGuarded($name))
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
    function reset(): void
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
    public function get($name, $default = null)
    {
        if ($method = $this->_mapPropertyToMethod($name))
            return $this->$method();

        if ($this->hasProperty($name))
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
    public function set($name, $value): static
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
        if ($this->hasProperty($name)) {
            if(property_exists($this, $name))
                return $this->$name ?? null;
            else
                return $this->__properties[$name] ?? null;
        }
    }

    protected function _setRaw($name, $value)
    {
        if ($this->isGuarded($name))
            return;

        $this->_map['public'][$name] = $name;
        $this->_map['dynamic'][$name] = $name;

        if(property_exists($this, $name))
            $this->$name = $value;
        else
            $this->__properties[$name] = $value;
    }

    protected function isGuarded($property)
    {
        return isset($property[0]) && $property[0] == '_'; //  && $property[1] == '_'
        //$property = new \ReflectionProperty($this, $property_name);
        //return !$property->isPublic();
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
        if (str_starts_with($method, 'get'))
            return lmbString::under_scores(substr($method, 3));
    }

    protected function _mapSetToProperty($method)
    {
        if (str_starts_with($method, 'set'))
            return lmbString::under_scores(substr($method, 3));
    }

    protected function _mapPropertyToMethod($property)
    {
        $hash = static::class . '::' . $property;

        if (array_key_exists($hash, self::$map_p2m))
            return self::$map_p2m[$hash];

        $capsed = lmbString::camel_case($property);
        $method = 'get' . $capsed;
        if ($method !== 'get' && method_exists($this, $method)) {
            self::$map_p2m[$hash] = $method;
            return $method;
        }
        //'is_foo' property is mapped to 'isFoo' method if it exists
        if (strpos($property, 'is_') === 0 && method_exists($this, $capsed)) {
            self::$map_p2m[$hash] = $capsed;
            return $capsed;
        }
        self::$map_p2m[$hash] = false;
        return false;
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
     * @return boolean whether this object contains $name
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
