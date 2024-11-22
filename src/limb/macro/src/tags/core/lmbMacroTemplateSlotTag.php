<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\tags\core;

use limb\macro\src\compiler\lmbMacroTag;

/**
 * class lmbMacroTemplateSlotTag.
 *  Very simple placeholder for {{apply:into}} tag
 * @tag template:slot
 * @package macro
 * @version $Id$
 */
class lmbMacroTemplateSlotTag extends lmbMacroTag
{
    protected function _generateContent($code_writer)
    {
        $parent_template_tag = $this->findParentByClass('limb\macro\src\tags\core\lmbMacroTemplateTag'); # DO NOT CHANGE
        $apply_tag = $parent_template_tag->getCurrentApplyTag();

        $intos = $apply_tag->findChildrenByClass('limb\macro\src\tags\core\lmbMacroApplyIntoTag');
        foreach ($intos as $into) {
            if ($into->get('slot') == $this->getNodeId())
                $into->generateNow($code_writer);
        }
    }
}
