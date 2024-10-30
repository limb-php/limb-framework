<?php

namespace limb\macro\tags\core;

use limb\macro\compiler\lmbMacroTag;

/**
 * class lmbMacroElseTag.
 * @tag else
 * @parent_tag_class limb\macro\tags\core\lmbMacroIfTag
 * @forbid_end_tag
 */
class lmbMacroElseTag extends lmbMacroTag
{
    protected function _generateContent($code_writer)
    {
        $code_writer->writePHP('} else {' . PHP_EOL);
    }
}
