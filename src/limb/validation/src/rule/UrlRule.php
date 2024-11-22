<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\validation\src\rule;

/**
 * Checks that field value is a valid url.
 * @package validation
 * @version $Id: UrlRule.php 7951 2009-06-16 17:48:42Z
 */
class UrlRule extends DomainRule
{
    function check($value)
    {
        $pattern = '#^' .
            '((?<protocol>https?|ftp)://)' .
            '(?<domain>[-A-Z0-9.]+)' .
            '(?<file>/[-A-Z0-9+&@\#/%=~_|!:,.;]*)?' .
            '(?<parameters>\?[-A-Z0-9+&@\#/%=~_|!:,.;]*)?' .
            '$#i';

        if (!preg_match($pattern, $value, $matches)) {
            $this->error('{Field} is not an url.');
        } else {
            parent::check($matches['domain']);
        }
    }
}
