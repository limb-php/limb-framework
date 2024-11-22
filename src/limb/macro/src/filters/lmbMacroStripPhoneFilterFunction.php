<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

function strip_phone($str)
{
    $str = strip_tags($str);
    return str_replace(array(' ', ')', '(', '-'), array('', '', '', ''), $str);
}
