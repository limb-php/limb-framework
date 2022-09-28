<?php
namespace limb\cms\src\macro;

use limb\macro\src\compiler\lmbMacroTag;

/**
 * class DeniedTag.
 * @tag denied
 * @req_attributes resource
 * @restrict_self_nesting
 */
class DeniedTag extends lmbMacroTag
{
  protected $_storage;
  const default_role = 'limb\toolkit\src\lmbToolkit::instance()->getMember()';

  protected function _generateContent($code)
  {
    $code->writePHP("if(!limb\\toolkit\\src\\lmbToolkit::instance()->getAcl()->isAllowed(");

    if(!$role = $this->getEscaped('role'))
      $role = self::default_role;
    $code->writePHP($role);

    $code->writePHP(', '.$this->getEscaped('resource'));

    if($privelege = $this->getEscaped('privelege'))
      $code->writePHP(', '.$privelege);

    $code->writePHP(')) {'.PHP_EOL);
    parent::_generateContent($code);
    $code->writePHP('}'.PHP_EOL);
  }
}
