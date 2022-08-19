<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\view\src;

use limb\core\src\lmbEnv;
use limb\toolkit\src\lmbToolkit;
use limb\fs\src\lmbFs;
use limb\core\src\exception\lmbException;
use limb\twig\src\lmbTwigExtension;

/**
 * class lmbTwigView.
 *
 * @package view
 * @version $Id$
 */
class lmbTwigView extends lmbView
{
  private $templateInstance;

  function __construct($template_name)
  {
    $pos = strrpos($template_name, '.');
    if($pos === false)
    {
      $template_name .= '.twig';
    }

    $this->template_name = $template_name;
  }

  function __call($methodName, $params)
  {
    $tpl = $this->getTwigTemplate();
    if(!method_exists($tpl, $methodName))
    {
      throw new lmbException(
          'Wrong template method called',
          array(
            'template class' => get_class($tpl),
            'method' => $methodName,
            )
          );
    }
    return call_user_func_array(array($tpl, $methodName), $params);
  }

  static function locateTemplateByAlias($alias)
  {
    $twig_conf = lmbToolkit::instance()->getTwigConfig();

    if(!lmbFs::isPathAbsolute($alias))
    {
       $tpl_path = lmbToolkit::instance()->tryFindFileByAlias($alias, $twig_conf->get('tmp_dirs'), 'twig');
       if( $tpl_path )
        return $alias;
    }
    elseif(file_exists($alias))
    {
       return $alias;
    }
  }

  function getTwigTemplate()
  {
    return $this->_getTwigTemplate();
  }

  protected function _getTwigTemplate()
  {
    if( $this->templateInstance )
      return $this->templateInstance;

    $twig_conf = lmbToolkit::instance()->getTwigConfig();

    $tmp_dirs = $twig_conf->get('tmp_dirs');

    $loader = new \Twig\Loader\FilesystemLoader( $tmp_dirs );

    if( lmbEnv::get('APP_TEMPLATE_THEME') )
    {
      foreach( $tmp_dirs as $dir )
        $loader->prependPath( $dir.'/_themes/' . lmbEnv::get('APP_TEMPLATE_THEME') );
    }

    $twig = new \Twig\Environment($loader, ['cache' => $twig_conf->get('cache'),
                                            'debug' => $twig_conf->get('debug'),
                                            'auto_reload' => $twig_conf->get('auto_reload'),
                                            ]);

    if( $twig_conf->get('debug') )
    {
      $twig->addExtension(new \Twig\Extension\DebugExtension());
    }

    $twig->addExtension(new lmbTwigExtension());

    $this->templateInstance = $twig->load( $this->getTemplate() );
    return $this->templateInstance;
  }

  function render()
  {
    return $this->getTwigTemplate()->render( $this->_fillTemplateVars() );
  }

  protected function _fillTemplateVars()
  {
    $params = array();

    foreach($this->getVariables() as $variable_name => $value)
      $params[ $variable_name ] = $value;

    foreach($this->forms_datasources as $form_id => $datasource)
      $params[ 'form_' . $form_id . '_datasource' ] = $datasource;

    foreach($this->forms_errors as $form_id => $error_list)
      $params[ 'form_' . $form_id . '_error_list' ] = $error_list->getArray();

    return $params;
  }
}
