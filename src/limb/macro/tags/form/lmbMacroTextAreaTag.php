<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\tags\form;

/**
 * Macro analog for html <textarea> tag
 * @tag textarea
 * @package macro
 * @version $Id$
 */
class lmbMacroTextAreaTag extends lmbMacroFormTagElement
{
    protected $html_tag = 'textarea';
    protected $widget_class_name = 'limb\macro\tags\form\lmbMacroTextAreaWidget';  # DO NOT CHANGE

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
