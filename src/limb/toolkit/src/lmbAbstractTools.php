<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\toolkit\src;

/**
 * Base class for most real applications tools
 * @see lmbToolkit
 * @package toolkit
 * @version $Id: lmbAbstractTools.php 7486 2009-01-26 19:13:20Z
 */
abstract class lmbAbstractTools implements lmbToolkitToolsInterface
{
    protected $reserved_methods = array('__construct', '_setRaw', '_getRaw', '_init');
    /**
     * @var lmbToolkit reference of lmbToolkit instance
     */
    protected $toolkit;

    function __construct()
    {
        $this->toolkit = lmbToolkit::instance();
    }

    static function getRequiredTools()
    {
        return [];
    }

    /**
     * Returns all methods of the childs classes except methods of lmbToolkitToolsInterface interface
     * @see lmbToolkitToolsInterface::getToolsSignatures()
     */
    function getToolsSignatures()
    {
        $methods = get_class_methods($this);
        $interface_methods = get_class_methods(lmbToolkitToolsInterface::class);

        $signatures = array();
        foreach ($methods as $method) {
            if (in_array($method, $this->reserved_methods) || in_array($method, $interface_methods))
                continue;
            $signatures[$method] = $this;
        }

        return $signatures;
    }

    protected function _setRaw($var, $value)
    {
        $this->toolkit->setRaw($var, $value);
    }

    protected function _getRaw($var)
    {
        return $this->toolkit->getRaw($var);
    }
}
