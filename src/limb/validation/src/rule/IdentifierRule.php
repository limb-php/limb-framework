<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\validation\src\rule;

/**
 * Checks that field value is an alpha-numeric string
 * @package validation
 * @version $Id: IdentifierRule.php 7486 2009-01-26 19:13:20Z
 */
class IdentifierRule extends lmbSingleFieldRule
{
    function check($value)
    {
        $value = "$value";

        if (!preg_match("/^[a-zA-Z0-9.-]+$/i", $value))
            $this->error('{Field} must contain only letters and numbers');
    }
}
