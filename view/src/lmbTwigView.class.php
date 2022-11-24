<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/view/src/lmbView.class.php');
lmb_require('limb/twig/src/twigFunctionLoader.class.php');

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
    return call_user_method_array($methodName, $tpl, $params);
  }

  static function locateTemplateByAlias($alias)
  {
    $twig_conf = lmbToolkit :: instance()->getTwigConfig();

    if(!lmbFs :: isPathAbsolute($alias))
    {
       $tpl_path = lmbToolkit :: instance()->tryFindFileByAlias($alias, $twig_conf->get('tmp_dirs'), 'twig');
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

    $twig_conf = lmbToolkit :: instance()->getTwigConfig();

    $loader = new \Twig\Loader\FilesystemLoader( $twig_conf->get('tmp_dirs') );
    $twig = new \Twig\Environment($loader, ['cache' => $twig_conf->get('cache'),
                                            'debug' => $twig_conf->get('debug'),
                                            'auto_reload' => $twig_conf->get('auto_reload'),
                                            ]);

    if( $twig_conf->get('debug') )
    {
      //$twig->addExtension(new \Twig\Extension\DebugExtension());
    }

    $functionLoader = new twigFunctionLoader( $twig );
    $functionLoader->load();

    $this->templateInstance = $twig->loadTemplate( $this->getTemplate() );
    return $this->templateInstance;
  }

  function render()
  {
    return $this->getTwigTemplate()->render( $this->getVariables() );
  }

}
