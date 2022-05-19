<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\cms\src\validation\rule;

use limb\validation\src\rule\lmbSingleFieldRule;

/**
 * class lmbTreeIdentifierRule.
 *
 * @package cms
 * @version $Id$
 */
class lmbTreeIdentifierRule extends lmbSingleFieldRule
{
  function check($value)
  {
    if(!preg_match('~^[a-zA-Z0-9-_\.]+$~', $value))
      return $this->error(lmb_i18n('{Field} may contain only digits, Latin characters and special characters `-`, `_`, `.`'));
  }
}


