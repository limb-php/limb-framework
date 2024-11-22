<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\tags\form;

use limb\macro\src\compiler\lmbMacroTag;

/**
 * @tag form:field_errors
 * @forbid_end_tag
 * @parent_tag_class limb\macro\src\tags\form\lmbMacroFormTag
 * @restrict_self_nesting
 * @package macro
 * @version $Id$
 */
class lmbMacroFormFieldErrorsTag extends lmbMacroTag
{
    protected function _generateContent($code_writer)
    {
        $form = $this
            ->findParentByClass('limb\macro\src\tags\form\lmbMacroFormTag') # DO NOT CHANGE
            ->getRuntimeVar();

        $to = $this->get('to');

        if ($this->has('for'))
            $for = $this->getEscaped('for');
        else
            $for = "";

        $code_writer->writePhp("{$to} = {$form}->getErrorsListForFields({$for});\n");

        parent::_generateContent($code_writer);
    }
}
