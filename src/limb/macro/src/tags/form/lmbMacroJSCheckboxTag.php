<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\tags\form;

/**
 * Checkbox that always sends something as a value.
 * Actually generates hidden input as well as checkbox. The checkbox used only to change hidden input value
 * @tag js_checkbox
 * @forbid_end_tag
 * @package macro
 * @version $Id$
 */
class lmbMacroJSCheckboxTag extends lmbMacroFormTagElement
{
    protected $html_tag = 'input';
    protected $widget_class_name = 'limb\macro\src\tags\form\lmbMacroJSCheckboxWidget'; # DO NOT CHANGE

    function preParse($compiler): void
    {
        parent::preParse($compiler);

        $this->set('type', 'checkbox');
    }

    function _generateContent($code_writer)
    {
        $code_writer->writePHP("{$this->getRuntimeVar()}->renderHidden();\n");
    }
}
