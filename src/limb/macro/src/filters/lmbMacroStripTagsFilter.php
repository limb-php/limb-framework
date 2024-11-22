<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\filters;

use limb\macro\src\compiler\lmbMacroFunctionBasedFilter;

/**
 * class lmbMacroStripTagsFilter.
 *
 * @filter striptags
 * @aliases notags, strip_tags
 * @package macro
 * @version $Id$
 */
class lmbMacroStripTagsFilter extends lmbMacroFunctionBasedFilter
{
    protected $function = 'strip_tags';
}
