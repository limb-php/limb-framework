<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_app\src\filter;

use limb\core\src\lmbEnv;
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
    protected $locale;

    /**
     * @uses LIMB_DEFAULT_LOCALE
     */
    function __construct($locale = 'en_US')
    {
        $this->locale = lmbEnv::get('LIMB_DEFAULT_LOCALE') ?? $locale;
    }

    function run($filter_chain, $request = null, $callback = null)
    {
        lmbToolkit::instance()->setLocale($this->locale);

        return $filter_chain->next($request, $callback);
    }
}
