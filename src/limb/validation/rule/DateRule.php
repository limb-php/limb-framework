<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\validation\rule;

use limb\datetime\lmbDateTime;

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

