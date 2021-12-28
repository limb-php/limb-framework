<?php
namespace limb\twig\src;

use limb\toolkit\src\lmbToolkit;
use limb\twig\src\Wysiwyg_TokenParser;

class lmbTwigExtension extends \Twig\Extension\AbstractExtension
{
  public function getFunctions()
  {
      return [
          new \Twig\TwigFunction('render', static::class.'::renderFragment', array('is_safe' => array('html'))),
          new \Twig\TwigFunction('controller', static::class.'::controller'),
          new \Twig\TwigFunction('file_exists', static::class.'::file_exists'),
          new \Twig\TwigFunction('copy_year', static::class.'::copy_year'),
          new \Twig\TwigFunction('form_datasource', static::class.'::form_datasource'),
          new \Twig\TwigFunction('form_errors', static::class.'::form_errors'),
          new \Twig\TwigFunction('route_url', static::class.'::route_url'),
      ];
  }

  public function getTokenParsers()
  {
      return [
        new Wysiwyg_TokenParser()
      ];
  }

  public function getFilters()
  {
    return [
      new \Twig\TwigFilter('number_format', 'number_format'),
      new \Twig\TwigFilter('odd_or_even', function ($number) {
        return ($number % 2) ? 'odd' : 'even' ;
      })
    ];
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

  public static function form_errors($form_id, $new_field_names = array())
  {
    $view = lmbToolkit::instance()->getView();

    $error_list = $view->getFormErrors($form_id);
    if( $error_list && !empty($new_field_names) )
      $error_list->renameFields( $new_field_names );

    return $error_list;
  }

  public static function form_datasource($form_id, $name)
  {
    $view = lmbToolkit::instance()->getView();
    $datasource = $view->getFormDatasource($form_id);

    return $datasource[ $name ];
  }

  public static function route_url($params, $route = '', $skip_controller = false)
  {
    $routes = lmbToolkit::instance()->getRoutesUrl($params, $route, $skip_controller);

    return $routes;
  }
}
