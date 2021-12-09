<?php
/*
 * depricated !!! uses with TWIG 2.x
 */

namespace limb\twig\src;

use limb\toolkit\src\lmbToolkit;
use Twig\TwigFunction;

class twigFunctionLoader
{
  protected $environment;

  public function __construct($environment)
  {
    $this->environment = $environment;
  }

  public function load()
  {
    foreach( $this->getFunctions() as $function )
    {
      $this->environment->addFunction($function);
    }
  }

  function getFunctions()
  {
      return array(
          new TwigFunction('render', static::class.'::renderFragment', array('is_safe' => array('html'))),
          new TwigFunction('controller', static::class.'::controller'),
          new TwigFunction('file_exists', static::class.'::file_exists'),
          new TwigFunction('copy_year', static::class.'::copy_year'),
          new TwigFunction('form_errors', static::class.'::form_errors'),
      );
  }

  function getTokenParsers()
  {
      return array(
      );
  }

  public static function renderFragment($uri, $options = array())
  {
    $strategy = isset($options['strategy']) ? $options['strategy'] : 'inline';
    unset($options['strategy']);

    return $uri->render($options);
  }

  public static function controller($controller, $options = array())
  {
    return call_user_func($controller, $options);
  }

  public static function file_exists($path, $options = array())
  {
    return file_exists($path);
  }

  public static function copy_year($start_year)
  {
    return $start_year . ((date('Y') != $start_year) ? '&ndash;' . date('Y') : '');
  }

  public static function form_errors($form_id)
  {
    $view = lmbToolkit::instance()->getView();

    return $view->getFormErrors($form_id);
  }

}
