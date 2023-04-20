<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\view\src\toolkit;

use limb\config\src\toolkit\lmbConfTools;
use limb\fs\src\toolkit\lmbFsTools;
use limb\toolkit\src\lmbAbstractTools;
use limb\toolkit\src\lmbToolkit;
use limb\core\src\exception\lmbException;
use limb\macro\src\lmbMacroTemplateLocator;
use limb\macro\src\lmbMacroConfig;
use limb\view\src\lmbMacroView;
use limb\view\src\lmbTwigView;

/**
 * class lmbViewTools.
 *
 * @package view
 * @version $Id$
 */
class lmbViewTools extends lmbAbstractTools
{
  protected $view_types = [
      '.phtml' => lmbMacroView::class,
      '.twig' => lmbTwigView::class
  ];
  protected $macro_config;
  protected $macro_locator;
  protected $twig_config;

  static function getRequiredTools()
  {
    return [
      lmbFsTools::class,
      lmbConfTools::class
    ];
  }

  function setSupportedViewTypes($types)
  {
    $this->view_types = $types;
  }

  function getSupportedViewTypes()
  {
    return $this->view_types;
  }

  function getSupportedViewExtensions(): array
  {
    return array_keys($this->view_types);
  }

  function locateTemplateByAlias($alias, $view_class = null)
  {
      if( !$view_class )
          $view_class = $this->_findViewClassByTemplate($alias);

      return call_user_func(array($view_class, 'locateTemplateByAlias'), $alias);
  }

  function createViewByTemplate($template_name, $vars = [])
  {
      $class = $this->_findViewClassByTemplate($template_name);

      return new $class($template_name, $vars);
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

  function getMacroLocator(): lmbMacroTemplateLocator
  {
    if(is_object($this->macro_locator))
      return $this->macro_locator;

    $config = lmbToolkit::instance()->getMacroConfig();
    $this->macro_locator = new lmbMacroTemplateLocator(new lmbMacroConfig($config));

    return $this->macro_locator;
  }

  function setMacroConfig($config): void
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

  function setTwigConfig($config): void
  {
    $this->twig_config = $config;
  }
}
