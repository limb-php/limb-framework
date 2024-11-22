<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\core\src;

/**
 * interface lmbCollectionInterface.
 *
 * @package core
 * @version $Id$
 */
interface lmbCollectionInterface extends \Iterator, \Countable, \ArrayAccess, \JsonSerializable
{
    function sort($params);

    function getArray();

    function at($pos);

    function paginate($offset, $limit);

    function getOffset();

    function getLimit();

    function countPaginated();
}
