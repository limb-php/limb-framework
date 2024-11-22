<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\tags\lists;

use limb\macro\src\compiler\lmbMacroTag;

/**
 * Renders a portion of the template if the current list row is even
 * @tag list:even
 * @parent_tag_class limb\macro\src\tags\lists\lmbMacroListItemTag
 * @package macro
 * @version $Id$
 */
class lmbMacroListRowEvenTag extends lmbMacroTag
{
    protected function _generateContent($code_writer)
    {
        $list = $this->findParentByClass('limb\\macro\\src\\tags\\lists\\lmbMacroListTag');  # DO NOT CHANGE
        $counter_var = $list->getCounterVar();

        $code_writer->writePHP('if((' . $counter_var . '+1) % 2 == 0) {');
        parent::_generateContent($code_writer);
        $code_writer->writePHP('}');
    }
}
