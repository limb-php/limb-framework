<?php
namespace limb\macro\src\tags\core;

use limb\macro\src\compiler\lmbMacroTag;

/**
 * class limb\macro\src\tags\core\lmbMacroOptionalTag.
 *
 * @tag optional
 * @req_attributes for
 */
class lmbMacroOptionalTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $for = $this->get('for');
    $tempvar = $code->generateVar();
    $code->writePHP("{$tempvar} = {$for};\n");

    $code->writePHP('if (is_scalar(' . $tempvar .' )) ' . $tempvar . '= trim(' . $tempvar . ');');
    $code->writePHP('if (!empty(' . $tempvar . ')){');

    parent::_generateContent($code);

    $code->writePHP('}');
  }
}
