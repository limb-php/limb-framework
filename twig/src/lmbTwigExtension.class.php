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
          new \Twig\TwigFunction('current_uri', static::class.'::current_uri'),
          new \Twig\TwigFunction('is_allowed', static::class.'::is_allowed'),
          new \Twig\TwigFunction('pager_url', static::class.'::pager_url'),
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
      new \Twig\TwigFilter('format_bytes', [$this, 'format_bytes']),
      new \Twig\TwigFilter('odd_or_even', function ($number) {
        return ($number % 2) ? 'odd' : 'even' ;
      }),
      new \Twig\TwigFilter('str_slice', [$this, 'str_slice'])
    ];
  }

  /* extension functions */
  public static function str_slice($string, $start, $length, $ending = '...')
  {
    $slingth = mb_strlen($string);

    return mb_substr($string, $start, $length) . (($slingth > $length) ? $ending : '');
  }

  public static function format_bytes($size)
  {
    $i = 0;
    while (floor($size / 1024) > 0)
    {
      ++$i;
      $size /= 1024;
    }

    $size = str_replace('.', ',', round($size, 1));
    switch ($i) {
      case 0: return $size .= ' Bytes';
      case 1: return $size .= ' KBytes';
      case 2: return $size .= ' MBytes';
      case 3: return $size .= ' GBytes';
      case 4: return $size .= ' TBytes';
    }
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

  public static function current_uri($replace_params = array())
  {
    $uri = lmbToolkit::instance()->getRequest()->getUri();

    if( !empty($replace_params) )
    {
      foreach ($replace_params as $name => $value)
        $uri->addQueryItem($name, $value);
    }

    return $uri;
  }

  public static function is_allowed($role, $resource = null, $privilege = null)
  {
    $acl = lmbToolkit::instance()->getAcl();
    return $acl->isAllowed($role, $resource, $privilege);
  }

  public static function pager_url($name = 'pager', $page = 1)
  {
    $uri = lmbToolkit::instance()->getRequest()->getUri();
    $curi = clone($uri);

    $curi->addQueryItem($name, $page);

    return $curi->toString();
  }
}
