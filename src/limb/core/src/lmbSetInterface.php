<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\core\src;

/**
 * interface lmbSetInterface.
 *
 * @package core
 * @version $Id$
 */
interface lmbSetInterface extends \ArrayAccess, \Iterator, \JsonSerializable
{
    function get($name, $default = null);

    function set($name, $value);

    function remove($name);

    function reset();

    function export();

    function import($values);

    function has($name);
}
