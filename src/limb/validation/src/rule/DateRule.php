<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\validation\src\rule;

use limb\datetime\src\lmbDateTime;

/**
 * Checks that field value is a valid date
 * @package validation
 * @version $Id$
 */
class DateRule extends lmbSingleFieldRule
{
    const TYPE_ISO = 1;

    protected $type;

    function __construct($field_name, $type = self::TYPE_ISO, $custom_error = null)
    {
        parent::__construct($field_name, $custom_error);

        $this->type = $type;
    }

    function check($value)
    {
        if ($this->type == self::TYPE_ISO) {
            if (!lmbDateTime::validate((string)$value))
                $this->error('{Field} is not a valid ISO formatted date(YYYY-MM-DD HH:MM).');
        }
    }
}
