<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\tags\form;

/**
 * Macro analog for html <textarea> tag
 * @tag textarea
 * @package macro
 * @version $Id$
 */
class lmbMacroTextAreaTag extends lmbMacroFormTagElement
{
    protected $html_tag = 'textarea';
    protected $widget_class_name = 'limb\macro\src\tags\form\lmbMacroTextAreaWidget';  # DO NOT CHANGE

    function preParse($compiler): void
    {
        parent::preParse($compiler);

        // always has closing tag
        $this->has_closing_tag = true;
    }

    protected function _generateContent($code_writer)
    {
        $textarea = $this->getRuntimeVar();
        $code_writer->writePHP("echo htmlspecialchars({$textarea}->getValue() ?? '', ENT_QUOTES);\n");
    }
}
