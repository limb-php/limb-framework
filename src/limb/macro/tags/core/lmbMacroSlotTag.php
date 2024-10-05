<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\tags\core;

use limb\macro\compiler\lmbMacroTag;

/**
 * class limb\macro\tags\core\lmbMacroSlotTag.
 *
 * @tag slot
 * @forbid_end_tag
 * @package macro
 * @version $Id$
 */
class lmbMacroSlotTag extends lmbMacroTag
{
    protected function _generateContent($code_writer)
    {
        $slot = $this->getNodeId();
        //calling slot handler in case of dynamic wrapping
        $code_writer->writePHP('if(isset($this->__slot_handlers_' . $slot . ')) {');
        $arg_str = $this->attributesIntoArrayString($skip = array('id', 'inline'));
        $code_writer->writePHP('foreach($this->__slot_handlers_' . $slot . ' as $__slot_handler_' . $slot . ') {');
        $code_writer->writePHP('call_user_func_array($__slot_handler_' . $slot . ', array(' . $arg_str . '));');
        $code_writer->writePHP('}}');

        if (!$this->getBool('inline')) {
            $args = $code_writer->generateVar();
            $method = $code_writer->beginMethod('__slotHandler' . self::generateUniqueId(), array($args . '= array()'));

            $code_writer->writePHP("if($args) extract($args);");

            parent::_generateContent($code_writer);

            $code_writer->endMethod();
            //$arg_str = $this->attributesIntoArrayString($skip = array('id', 'inline'));
            $code_writer->writePHP('$this->' . $method . '(' . $arg_str . ');');
        } else
            parent::_generateContent($code_writer);
    }
}
