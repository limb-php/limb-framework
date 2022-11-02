<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\web_app\src\filter;

use limb\filter_chain\src\lmbInterceptingFilterInterface;
use limb\toolkit\src\lmbToolkit;

/**
 * class lmbDefaultLocaleFilter.
 *
 * @package web_app
 * @version $Id: lmbDefaultLocaleFilter.php 7486 2009-01-26 19:13:20Z
 */
class lmbDefaultLocaleFilter implements lmbInterceptingFilterInterface
{
  protected $default_locale;

  function __construct($default_locale = 'en_US')
  {
      $this->default_locale = $default_locale;
  }

  function run($filter_chain, $request = null, $response = null)
  {
      lmbToolkit::instance()->setLocale($this->default_locale);

      $response = $filter_chain->next($request, $response);

      return $response;
  }
}
