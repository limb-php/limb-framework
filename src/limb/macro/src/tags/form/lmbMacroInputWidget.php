<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\tags\form;

use limb\macro\src\tags\form\lmbMacroFormElementWidget;

/**
 * class lmbMacroInputWidget
 * A runtime widget for input tag of "text" and "hidden" types
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroInputWidget extends lmbMacroFormElementWidget
{
    function renderAttributes()
    {
        if (!$this->hasAttribute('value')) {
            $value = $this->getValue();
            if ($value) {
                $this->setAttribute('value', $value);
            } else {
                if (($value === 0) || ($value === 0.0))
                    $this->setAttribute('value', $value);
                else
                    $this->setAttribute('value', "");
            }
        }

        parent:: renderAttributes();
    }
}

