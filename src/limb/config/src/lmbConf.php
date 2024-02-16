<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\config\src;

use limb\core\src\exception\lmbException;
use limb\core\src\exception\lmbNoSuchPropertyException;
use limb\core\src\lmbObject;
use limb\fs\src\exception\lmbFileNotFoundException;
use limb\core\src\exception\lmbInvalidArgumentException;

/**
 * class lmbConf.
 *
 * @package config
 * @version $Id: lmbConf.php 8038 2010-01-19 20:19:00Z
 */
class lmbConf extends lmbObject
{
    protected $_file;

    function __construct($file)
    {
        $this->_file = $file;

        $conf = $this->_attachConfFile($file);

        foreach (['.override'] as $ending) {
            if ($override_file = $this->_getOverrideFile($file, $ending)) {
                $conf = $this->_attachConfFile($override_file, $conf);
            }
        }

        parent::__construct($conf);
    }

    protected function _attachConfFile($file, $existed_conf = array())
    {
        $conf = $existed_conf;
        if (!file_exists($file))
            throw new lmbFileNotFoundException("Config file '$file' not found");

        $config = include($file);

        if ($config === 1) {
            if (!is_array($conf))
                throw new lmbException("Config must be an array", array('file' => $file, 'content' => $conf));

            return $conf;
        } elseif (!is_array($config)) {
            throw new lmbException("Config must return an array", array('file' => $file, 'content' => $config));
        }

        return array_merge($existed_conf, $config);
    }

    protected function _getOverrideFile($file_path, $ending = '.override')
    {
        $file_name = substr($file_path, 0, strpos($file_path, '.php'));
        $override_file_name = $file_name . $ending . '.php';

        if (file_exists($override_file_name))
            return $override_file_name;
        else
            return false;
    }

    function get($name, $default = null)
    {
        if (!$name)
            throw new lmbInvalidArgumentException('Option name not given');

        $result = parent::get($name);
        if (null === $default && null !== $result)
            return $result;

        throw new lmbNoSuchPropertyException('Option "' . $name . '" not found', array('config' => $this->_file));
    }
}
