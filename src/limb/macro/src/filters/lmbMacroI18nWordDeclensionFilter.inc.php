<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

use limb\i18n\src\lmbI18n;

function lmb_macro_i18n_choose_declension_by_number($number, $singular_form, $plural_main_form, $plural_other_form, $locale, $domain = null)
{
    if (substr($number, -2) == 11)
        return lmbI18n::translate($plural_main_form, $locale, $domain);

    if (substr($number, -1) == 1)
        return lmbI18n::translate($singular_form, $locale, $domain);

    if (in_array(substr($number, -1), array(2, 3, 4))) {
        if ($number > 10 and (in_array(substr($number, -2), array(12, 13, 14))))
            return lmbI18n::translate($plural_main_form, $locale, $domain);
        else
            return lmbI18n::translate($plural_other_form, $locale, $domain);
    }
    return lmbI18n::translate($plural_main_form, $locale, $domain);
}
