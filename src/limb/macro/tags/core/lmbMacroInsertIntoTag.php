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
 * class limb\macro\tags\core\lmbMacroInsertIntoTag.
 *
 * @tag insert:into
 * @aliases into, wrap:into, include:into
 * @req_attributes slot
 * @package macro
 * @version $Id$
 */
class lmbMacroInsertIntoTag extends lmbMacroTag
{
    protected $slot_node;
    protected $is_dynamic;

    function preParse($compiler): void
    {
        parent::preParse($compiler);

        if ($slot_node = $this->parent->findUpChild($this->get('slot')))
            $this->is_dynamic = false;
        else
            $this->is_dynamic = true;

        if (!$this->is_dynamic) {
            $tree_builder = $compiler->getTreeBuilder();
            $tree_builder->pushCursor($slot_node, $this->location);
        }
    }

    function generate($code)
    {
        if (!$this->is_dynamic)
            parent::generate($code);
    }

    function generateNow($code)
    {
        parent::generate($code);
    }
}
