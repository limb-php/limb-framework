<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\macro\src\tags\form;

use limb\macro\src\compiler\lmbMacroTag;

/**
 * @tag select_options_export
 * @req_attributes from, to, text_field, key_field
 * @forbid_end_tag
 * @package macro
 * @version $Id$
 */
class lmbMacroSelectOptionsExportTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $to = $this->get('to');
    $from = $this->get('from');

    $key_field = $this->get('key_field');
    $text_field = $this->get('text_field');
    $class_field = $this->get('class_field');
    $disabled_field = $this->get('disabled_field', 'disabled');

    $options = $code->generateVar();
    $code->writePHP("{$options} = array();\n");

    $code->writePHP("foreach({$from} as \$item) {\n");

    $code->writePHP("if(isset(\$item['{$key_field}']) && isset(\$item['{$text_field}'])){\n");
      $code->writePHP("{$options}[\$item['{$key_field}']] = array() ;\n");
      $code->writePHP("{$options}[\$item['{$key_field}']]['text'] = \$item['{$text_field}'] ;\n");
      $code->writePHP("if( '{$class_field}' )\n");
        $code->writePHP("{$options}[\$item['{$key_field}']]['class'] = isset(\$item['{$class_field}']) ? \$item['{$class_field}'] : '' ;\n");
      $code->writePHP("if( '{$disabled_field}' )\n");
        $code->writePHP("{$options}[\$item['{$key_field}']]['disabled'] = isset(\$item['{$disabled_field}']) ? \$item['{$disabled_field}'] : false ;\n");
    $code->writePHP("}\n");

    $code->writePHP("}\n");

    $code->writePHP("{$to} = {$options};\n");
  }
}
