<?php

namespace limb\macro\tags\core;

use limb\macro\compiler\lmbMacroTag;

/**
 * class limb\macro\tags\core\lmbMacroDefaultTag.
 *
 * @tag default
 * @req_attributes for
 */
class lmbMacroDefaultTag extends lmbMacroTag
{
    protected function _generateContent($code_writer)
    {
        $for = $this->get('for');
        $tempvar = $code_writer->generateVar();
        $code_writer->writePHP("{$tempvar} = {$for};\n");

        $code_writer->writePHP('if (is_scalar(' . $tempvar . ' )) ' . $tempvar . ' = trim(' . $tempvar . ');');
        $code_writer->writePHP('if (empty(' . $tempvar . ')) {');

        parent::_generateContent($code_writer);

        $code_writer->writePHP('}');
    }
}
