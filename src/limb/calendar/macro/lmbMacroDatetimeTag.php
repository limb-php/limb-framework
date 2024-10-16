<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\calendar\macro;

use limb\macro\tags\form\lmbMacroFormTagElement;

/**
 * @tag datetime
 * @forbid_end_tag
 * @package calendar
 * @version $Id$
 */
class lmbMacroDatetimeTag extends lmbMacroFormTagElement
{
    protected $html_tag = 'input';
    protected $widget_class_name = 'limb\calendar\lmbCalendarWidget';

    function preParse($compiler): void
    {
        $this->set('type', 'text');

        parent::preParse($compiler);
    }

    protected function _generateAfterClosingTag($code)
    {
        parent::_generateAfterClosingTag($code);

        $widget = $this->getRuntimeVar();
        $code->writePHP("{$widget}->renderCalendar();\n");
    }
}
