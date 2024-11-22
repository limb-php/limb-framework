<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\tags\form;

use limb\macro\src\compiler\lmbMacroRuntimeWidgetHtmlTag;

/**
 * Base class for any form element tag
 */
class lmbMacroFormTagElement extends lmbMacroRuntimeWidgetHtmlTag
{
    function _generateWidget($code_writer)
    {
        parent::_generateWidget($code_writer);
        if ($form_tag = $this->findParentByClass('limb\macro\src\tags\form\lmbMacroFormTag')) {  # DO NOT CHANGE
            $code_writer->writeToInit("{$this->getRuntimeVar()}->setForm({$form_tag->getRuntimeVar()});\n");
            $code_writer->writeToInit("{$form_tag->getRuntimeVar()}->addChild({$this->getRuntimeVar()});\n");
        }
    }

    function getRuntimeVar()
    {
        if ($this->runtime_var)
            return $this->runtime_var;

        $this->runtime_var = '$this->' . $this->tag . '_' . self::generateNewRuntimeId();
        return $this->runtime_var;
    }
}
