<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\Config;

use Limb\Core\Exception\lmbException;
use Limb\Core\Exception\lmbNoSuchPropertyException;
use Limb\Core\lmbSet;
use Limb\Fs\Exception\lmbFileNotFoundException;
use Limb\Core\Exception\lmbInvalidArgumentException;

/**
 * class lmbConf.
 *
 * @package config
 * @version $Id: lmbConf.php 8038 2010-01-19 20:19:00Z
 */
class lmbConf extends lmbSet
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

        if(parent::has($name))
            return parent::get($name, $default);

        if (null !== $default)
            return $default;

        throw new lmbNoSuchPropertyException('Option "' . $name . '" not found', array('config' => $this->_file));
    }
}
