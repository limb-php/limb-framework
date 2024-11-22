<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\validation\src\rule;

use limb\i18n\src\datetime\lmbLocaleDateTime;
use limb\toolkit\src\lmbToolkit;
use limb\i18n\src\lmbI18n;

/**
 * class LocaleDateRule.
 *
 * @package validation
 * @version $Id$
 */
class LocaleDateRule extends lmbSingleFieldRule
{
    protected $locale;

    function __construct($field_name, $locale = null)
    {
        $this->locale = $locale;
        parent::__construct($field_name);
    }

    function check($value)
    {
        $toolkit = lmbToolkit::instance();

        if (!$this->locale)
            $this->locale = $toolkit->getLocaleObject();

        if (!lmbLocaleDateTime::isLocalStringValid($this->locale, $value))
            $this->error(lmbI18n::translate('{Field} must have a valid date format', 'validation'));
    }
}
