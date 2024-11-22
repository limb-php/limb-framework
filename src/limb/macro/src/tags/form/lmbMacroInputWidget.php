<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\tags\form;

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

        parent::renderAttributes();
    }
}
