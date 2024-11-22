<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\filters;

use limb\macro\src\compiler\lmbMacroFunctionBasedFilter;

/**
 * @filter utf8_encode
 * @aliases utf8encode
 * @package macro
 * @version $Id$
 */
class lmbMacroUtf8EncodeFilter extends lmbMacroFunctionBasedFilter
{
    protected $function = 'utf8_encode';
}


