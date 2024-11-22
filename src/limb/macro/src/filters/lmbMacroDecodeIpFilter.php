<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\filters;

use limb\macro\src\compiler\lmbMacroFunctionBasedFilter;

/**
 * class lmbMacroDecodeIpFilter.
 *
 * @filter decode_ip
 * @package macro
 * @version $Id$
 */
class lmbMacroDecodeIpFilter extends lmbMacroFunctionBasedFilter
{
    protected $function = array('limb\net\src\lmbIp', 'decode');
}
