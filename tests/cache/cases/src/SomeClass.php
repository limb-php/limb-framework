<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\cache\cases\src;

use limb\toolkit\src\lmbToolkit;

class SomeClass
{
    static function Foo($param)
    {
        $cache = lmbToolkit::instance()->getCache();
        if ($value = $cache->get('bar' . $param, array('group' => 'bar_group'))) {
            return $value;
        }

        $value = 'bar_value_' . $param;

        $cache->set('bar' . $param, $value, array('group' => 'bar_group'));

        return $value;
    }
}
