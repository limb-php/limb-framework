<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\tags\form;

use limb\macro\compiler\lmbMacroRuntimeWidgetHtmlTag;

/**
 * Base class for any form element tag
 */
class lmbMacroFormTagElement extends lmbMacroRuntimeWidgetHtmlTag
{
    function _generateWidget($code_writer)
    {
        parent::_generateWidget($code_writer);
        if ($form_tag = $this->findParentByClass('limb\macro\tags\form\lmbMacroFormTag')) {  # DO NOT CHANGE
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
