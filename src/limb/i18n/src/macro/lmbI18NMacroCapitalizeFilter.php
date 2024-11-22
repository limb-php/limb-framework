<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\i18n\src\macro;

use limb\macro\src\compiler\lmbMacroFunctionBasedFilter;

/**
 * @filter i18n_capitalize
 * @package i18n
 * @version $Id$
 */
class lmbI18NMacroCapitalizeFilter extends lmbMacroFunctionBasedFilter
{
    protected $function = array('limb\i18n\src\charset\lmbI18nString', 'ucfirst');
}
