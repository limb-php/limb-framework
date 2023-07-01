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

  protected function _generateContent($code_writer)
  {
    $code_writer->writePHP("if(!limb\\toolkit\\src\\lmbToolkit::instance()->getAcl()->isAllowed(");

    if(!$role = $this->getEscaped('role'))
      $role = self::default_role;
    $code_writer->writePHP($role);

    $code_writer->writePHP(', '.$this->getEscaped('resource'));

    if($privilege = $this->getEscaped('privilege'))
      $code_writer->writePHP(', '.$privilege);

    $code_writer->writePHP(')) {'.PHP_EOL);
    parent::_generateContent($code_writer);
    $code_writer->writePHP('}'.PHP_EOL);
  }
}
