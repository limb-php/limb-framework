<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

namespace limb\i18n\macro;

use limb\i18n\charset\lmbI18nString;
use limb\macro\compiler\lmbMacroFunctionBasedFilter;

/**
 * @filter i18n_capitalize
 * @package i18n
 * @version $Id$
 */
class lmbI18NMacroCapitalizeFilter extends lmbMacroFunctionBasedFilter
{
    protected $function = array(
        lmbI18nString::class, 'ucfirst'
    );
}
