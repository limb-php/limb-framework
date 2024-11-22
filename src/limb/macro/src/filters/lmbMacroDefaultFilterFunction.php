<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

function lmb_macro_apply_default($value, $default)
{
    if (empty($value) && $value !== "0" && $value !== 0)
        return $default;
    else
        return $value;
}

