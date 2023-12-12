<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cms\src\validation\rule;

use limb\validation\src\rule\lmbSingleFieldRule;
use limb\i18n\src\lmbI18n;

/**
 * class TreeIdentifierRule.
 *
 * @package cms
 * @version $Id$
 */
class TreeIdentifierRule extends lmbSingleFieldRule
{
    function check($value)
    {
        if (!preg_match('~^[a-zA-Z0-9-_\.]+$~', $value)) {
            $this->error(lmbI18n::translate('{Field} may contain only digits, Latin characters and special characters `-`, `_`, `.`'));
        }
    }
}
