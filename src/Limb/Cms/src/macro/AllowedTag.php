<?php

namespace limb\cms\macro;

use limb\macro\compiler\lmbMacroTag;

/**
 * class limb\cms\macro\AllowedTag.
 * @tag allowed
 * @req_attributes resource
 * @restrict_self_nesting
 */
class AllowedTag extends lmbMacroTag
{
    protected $_storage;
    const default_role = 'limb\toolkit\lmbToolkit::instance()->getMember()';

    protected function _generateContent($code_writer)
    {
        $code_writer->writePHP("if(limb\\toolkit\\lmbToolkit::instance()->getAcl()->isAllowed(");

        if (!$role = $this->getEscaped('role'))
            $role = self::default_role;
        $code_writer->writePHP($role);

        $code_writer->writePHP(', ' . $this->getEscaped('resource'));

        if ($privilege = $this->getEscaped('privilege'))
            $code_writer->writePHP(', ' . $privilege);

        $code_writer->writePHP(')) {' . PHP_EOL);
        parent::_generateContent($code_writer);
        $code_writer->writePHP('}' . PHP_EOL);
    }
}
