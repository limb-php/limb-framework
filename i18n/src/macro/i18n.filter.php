<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\i18n\src\macro;

/**
 * Filter i18n for macro templates
 * @filter i18n
 * @package i18n
 * @version $Id$
 */
class lmbI18NMacroFilter extends lmbMacroFunctionBasedFilter
{
  protected $function = 'lmb_i18n';

  protected function _getBaseValue()
  {
    $base_value = parent::_getBaseValue();
    return "$base_value";
  }
}