<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\calendar\src\macro;

use limb\macro\src\tags\form\lmbMacroFormTagElement;

/**
 * @tag date3select
 * @forbid_end_tag
 * @req_attributes id
 * @package calendar
 * @version $Id: $
 */
class lmbDate3SelectTag extends lmbMacroFormTagElement
{
    protected $html_tag = 'input';
    protected $widget_class_name = 'limb\calendar\src\lmbDate3SelectWidget';

    function preParse($compiler): void
    {
        $this->set('type', 'hidden');

        parent::preParse($compiler);
    }

    protected function _generateAfterClosingTag($code)
    {
        parent::_generateAfterClosingTag($code);
        $widget = $this->getRuntimeVar();
        $code->writePHP("{$widget}->renderDate3Select();\n");
    }
}
