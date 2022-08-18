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
 * @version $Id: lmbDefaultLocaleFilter.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbDefaultLocaleFilter implements lmbInterceptingFilterInterface
{
  protected $default_locale;
  protected $toolkit;

  function __construct($default_locale)
  {
    $this->default_locale = $default_locale;
    $this->toolkit = lmbToolkit::instance();
  }

  function run($filter_chain)
  {
    $this->toolkit->setLocale($this->default_locale);
    $filter_chain->next();
  }
}


