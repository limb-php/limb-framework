<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\active_record\src;

use limb\core\src\lmbDecorator;

/**
 * class lmbARProxy.
 *
 * @package active_record
 * @version $Id$
 */
class lmbARProxy
{
    static function generate($proxy_class, $proxied_class)
    {
        return lmbDecorator::generate($proxied_class, $proxy_class, new lmbARProxyGeneratorEventsHandler());
    }
}
