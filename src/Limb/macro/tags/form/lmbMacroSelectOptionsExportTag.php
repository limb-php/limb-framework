<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\tags\form;

use limb\macro\compiler\lmbMacroTag;

/**
 * @tag select_options_export
 * @req_attributes from, to, text_field, key_field
 * @forbid_end_tag
 * @package macro
 * @version $Id$
 */
class lmbMacroSelectOptionsExportTag extends lmbMacroTag
{
    protected function _generateContent($code_writer)
    {
        $to = $this->get('to');
        $from = $this->get('from');

        $key_field = $this->get('key_field');
        $text_field = $this->get('text_field');
        $class_field = $this->get('class_field');
        $disabled_field = $this->get('disabled_field', 'disabled');

        $options = $code_writer->generateVar();
        $code_writer->writePHP("{$options} = array();\n");

        $code_writer->writePHP("foreach({$from} as \$item) {\n");

        $code_writer->writePHP("if(isset(\$item['{$key_field}']) && isset(\$item['{$text_field}'])){\n");
        $code_writer->writePHP("{$options}[\$item['{$key_field}']] = array() ;\n");
        $code_writer->writePHP("{$options}[\$item['{$key_field}']]['text'] = \$item['{$text_field}'] ;\n");
        $code_writer->writePHP("if( '{$class_field}' )\n");
        $code_writer->writePHP("{$options}[\$item['{$key_field}']]['class'] = isset(\$item['{$class_field}']) ? \$item['{$class_field}'] : '' ;\n");
        $code_writer->writePHP("if( '{$disabled_field}' )\n");
        $code_writer->writePHP("{$options}[\$item['{$key_field}']]['disabled'] = isset(\$item['{$disabled_field}']) ? \$item['{$disabled_field}'] : false ;\n");
        $code_writer->writePHP("}\n");

        $code_writer->writePHP("}\n");

        $code_writer->writePHP("{$to} = {$options};\n");
    }
}
