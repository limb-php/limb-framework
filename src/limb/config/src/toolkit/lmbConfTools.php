<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\config\src\toolkit;

use limb\fs\src\toolkit\lmbFsTools;
use limb\toolkit\src\lmbAbstractTools;
use limb\core\src\lmbObject;
use limb\core\src\lmbEnv;
use limb\config\src\lmbIni;
use limb\config\src\lmbYaml;
use limb\config\src\lmbCachedIni;
use limb\config\src\lmbConf;
use limb\fs\src\lmbFs;
use limb\core\src\exception\lmbException;
use limb\fs\src\exception\lmbFileNotFoundException;

lmbEnv::setor('LIMB_CONF_INCLUDE_PATH', 'settings;src/limb/*/settings');

/**
 * class lmbConfTools.
 *
 * @package config
 * @version $Id: lmbConfTools.php 8142 2010-03-01 20:48:06Z
 */
class lmbConfTools extends lmbAbstractTools
{
  protected $confs = array();
  protected $conf_include_path;

  static function getRequiredTools()
  {
    return [
      lmbFsTools::class
    ];
  }

  function setConf($name, $conf): void
  {
    $this->confs[$this->_normalizeConfName($name)] = $conf;
  }

  function hasConf($name): bool
  {
    try {
      $this->toolkit->getConf($name);
      return true;
    }
    catch (lmbFileNotFoundException $e)
    {
      return false;
    }
  }

  function setConfIncludePath($path): void
  {
    $this->conf_include_path = $path;
  }

  function getConfIncludePath()
  {
    if(!$this->conf_include_path)
      $this->conf_include_path = lmbEnv::get('LIMB_CONF_INCLUDE_PATH');
    return $this->conf_include_path;
  }

  protected function _locateConfFiles($name)
  {
    return $this->toolkit->findFileByAlias($name, $this->toolkit->getConfIncludePath(), 'config', false);
  }

  function getConf($name)
  {
    $name = $this->_normalizeConfName($name);

    if(isset($this->confs[$name]))
      return $this->confs[$name];

    if(preg_match("/\.ini$/", $name)) {
      $file = $this->_locateConfFiles($name);
      if( lmbEnv::has('LIMB_VAR_DIR') )
        $this->confs[$name] = new lmbCachedIni($file, lmbEnv::get('LIMB_VAR_DIR') . '/ini/');
      else
        $this->confs[$name] = new lmbIni($file);
    }
    elseif(preg_match("/\.yml$/", $name)) {
      $file = $this->_locateConfFiles($name);

      $this->confs[$name] =  $this->parseYamlFile(lmbFs::normalizePath($file));

    }
    elseif(preg_match("/\.conf\.php$/", $name)) {
      $file = $this->_locateConfFiles($name);
      if(empty($file))
        throw new lmbFileNotFoundException($name);

      $this->confs[$name] = new lmbConf(lmbFs::normalizePath($file));
    }
    else {
        throw new lmbException("'$name' configuration is not supported!");
    }

    return $this->confs[$name];
  }

    public function getConfParam($conf_param, $default = null)
    {
        [$conf, $param] = array_merge( explode('.', $conf_param), [null, null] );

        if($param === null) {
            return $this->getConf($conf)->export();
        }

        return $this->getConf($conf)->get($param, $default);
    }

  protected function _normalizeConfName($name)
  {
    if(preg_match("/(\.ini|\.yml|\.conf\.php)$/", $name))
      return $name;
    return "$name.conf.php";
  }

  protected function parseYamlFile($file): lmbObject
  {
    $yml = lmbYaml::load($file);
    return new lmbObject($yml);
  }
}
