<?php
namespace limb\twig\src;

use limb\toolkit\src\lmbToolkit;
use limb\twig\src\Wysiwyg_TokenParser;

class lmbTwigExtension extends \Twig\Extension\AbstractExtension
{
  public function getFunctions()
  {
      return array(
          new \Twig\TwigFunction('render', static::class.'::renderFragment', array('is_safe' => array('html'))),
          new \Twig\TwigFunction('controller', static::class.'::controller'),
          new \Twig\TwigFunction('file_exists', static::class.'::file_exists'),
          new \Twig\TwigFunction('copy_year', static::class.'::copy_year'),
          new \Twig\TwigFunction('form_errors', static::class.'::form_errors'),
      );
  }

  public function getTokenParsers()
  {
      return [new Wysiwyg_TokenParser()];
  }

  /* extension functions */
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
