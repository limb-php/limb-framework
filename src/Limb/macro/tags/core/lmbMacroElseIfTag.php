<?php

namespace limb\macro\tags\core;

use limb\macro\compiler\lmbMacroTag;
use limb\macro\lmbMacroException;

/**
 * class lmbMacroElseIfTag.
 * @tag elseif
 * @parent_tag_class limb\macro\tags\core\lmbMacroIfTag
 * @forbid_end_tag
 */
class lmbMacroElseIfTag extends lmbMacroTag
{
    function preParse($compiler): void
    {
        if (!$this->has('var') && !$this->has('expr'))
            throw new lmbMacroException("'var'( alias 'expr') attribute is required for 'elseif' tag");
        parent::preParse($compiler);
    }

    protected function _generateContent($code_writer)
    {
        $var = $this->has('var') ? $this->get('var') : $this->get('expr');
        $code_writer->writePHP('} elseif(' . $var . ') {' . PHP_EOL);
    }
}
