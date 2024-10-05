<?php

namespace limb\macro\tags\core;

use limb\macro\compiler\lmbMacroTag;

/**
 * class lmbMacroCopyTag.
 * @tag copy
 * @req_attributes into
 * @restrict_self_nesting
 */
class lmbMacroCopyTag extends lmbMacroTag
{
    protected function _generateContent($code_writer)
    {
        $code_writer->writePHP("ob_start();\n");
        parent::_generateContent($code_writer);
        $code_writer->writePHP($this->get('into') . " = ob_get_contents();\n");
        $code_writer->writePHP("ob_end_flush();\n");
    }
}
