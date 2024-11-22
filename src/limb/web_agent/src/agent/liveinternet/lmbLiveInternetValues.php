<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_agent\src\agent\liveinternet;

use limb\web_agent\src\lmbWebAgentValues;

/**
 * Values of Liveinternet agent
 *
 * @package web_agent
 * @version $Id: lmbLiveInternetValues.php 89 2007-10-12 15:28:50Z CatMan $
 */
class lmbLiveInternetValues extends lmbWebAgentValues
{

    function buildQuery($encoding = 'utf-8')
    {
        $vars = array();
        foreach ($this->convert($encoding) as $name => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    $vars[] = http_build_query(array($name => $v), '', ';');
                }
            } else
                $vars[] = http_build_query(array($name => $value), '', ';');
        }
        return implode(';', $vars);
    }

}
