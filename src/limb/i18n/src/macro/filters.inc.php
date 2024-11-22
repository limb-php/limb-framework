<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

use limb\i18n\src\datetime\lmbLocaleDateTime;
use limb\toolkit\src\lmbToolkit;

function lmb_i18n_date_filter($params, $value)
{
    $toolkit = lmbToolkit::instance();
    if (isset($params[0]) && $params[0]) {
        $locale = $toolkit->getLocaleObject($params[0]);
    } else
        $locale = $toolkit->getLocaleObject();

    if (isset($params[3]) && $params[3])
        $format = $params[3];
    else {
        if (isset($params[2]) && $params[2])
            $format_type = $params[2];
        else
            $format_type = 'short_date';

        $property = $format_type . '_format';
        $format = $locale->$property;
    }

    if (isset($params[1]) && $params[1])
        $date_type = $params[1];
    else
        $date_type = 'stamp';

    switch ($date_type) {
        case 'stamp':
            $date = new lmbLocaleDateTime((int)$value);
            break;
        default:
            $date = new lmbLocaleDateTime($value);
            break;
    }

    return $date->localeStrftime($format, $locale);
}
