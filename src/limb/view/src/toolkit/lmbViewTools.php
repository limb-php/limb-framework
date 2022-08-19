<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\view\src\toolkit;

use limb\toolkit\src\lmbAbstractTools;
use limb\toolkit\src\lmbToolkit;
use limb\core\src\lmbEnv;
use limb\core\src\exception\lmbException;
use limb\macro\src\lmbMacroTemplateLocator;
use limb\macro\src\lmbMacroConfig;

lmbEnv::setor('LIMB_SUPPORTED_VIEW_TYPES', '.phtml=limb\view\src\lmbMacroView;.twig=limb\view\src\lmbTwigView');

/**
 * class lmbViewTools.
 *
 * @package view
 * @version $Id$
 */
class lmbViewTools extends lmbAbstractTools
{
  protected $view_types = array();
  protected $macro_config;
  protected $macro_locator;
  protected $twig_config;

  function __construct()
  {
    parent::__construct();

    $items = explode(';', lmbEnv::get('LIMB_SUPPORTED_VIEW_TYPES'));
    foreach($items as $item)
    {
      list($ext, $class) = explode('=', $item);
      $this->view_types[$ext] = $class;
    }
  }

  function setSupportedViewTypes($types)
  {
    $this->view_types = $types;
  }

  function getSupportedViewTypes()
  {
    return $this->view_types;
  }

  function getSupportedViewExtensions()
  {
    return array_keys($this->view_types);
  }

  function locateTemplateByAlias($alias)
  {
    $class = $this->_findViewClassByTemplate($alias);

    return call_user_func(array($class, 'locateTemplateByAlias'), $alias);
  }

  function createViewByTemplate($template_name)
  {
    $class = $this->_findViewClassByTemplate($template_name);

    $view = new $class($template_name);
    return $view;
  }

  protected function _findViewClassByTemplate($template_name)
  {
    $pos = strrpos($template_name, '.');
    if($pos === false)
    {
      $ext = key($this->view_types);

      if( !$ext )
        throw new lmbException("Could not determine template type for file '$template_name'");
    }
    else
    {
      $ext = substr($template_name, $pos);
    }

    if(!isset($this->view_types[$ext]))
      throw new lmbException("Template extension '$ext' is not supported");

    return $this->view_types[$ext];
  }

  function getMacroConfig()
  {
    if(!$this->macro_config)
    {
      if(!is_object($config = $this->toolkit->getConf('macro')))
        throw new lmbException("Macro configuration not found");

      $this->macro_config = $config;
    }

    return $this->macro_config;
  }

  function getMacroLocator()
  {
    if(is_object($this->macro_locator))
      return $this->macro_locator;

    $config = lmbToolkit::instance()->getMacroConfig();
    $this->macro_locator = new lmbMacroTemplateLocator(new lmbMacroConfig($config));

    return $this->macro_locator;
  }

  function setMacroConfig($config)
  {
    $this->macro_config = $config;
  }

  function getTwigConfig()
  {
    if(!$this->twig_config)
    {
      if(!is_object($config = $this->toolkit->getConf('twig')))
        throw new lmbException("Twig configuration not found");

      $this->twig_config = $config;
    }

    return $this->twig_config;
  }

  function setTwigConfig($config)
  {
    $this->twig_config = $config;
  }
}

