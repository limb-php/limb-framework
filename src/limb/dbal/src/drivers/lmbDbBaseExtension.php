<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers;

use limb\dbal\src\exception\lmbDbException;

/**
 * class lmbBaseDbExtension.
 * A base class for all vendor specific extensions
 *
 * @package dbal
 * @version $Id$
 */
class lmbDbBaseExtension
{
    protected $connection;

    function __construct(lmbDbBaseConnection $conn)
    {
        $this->connection = $conn;
    }

    function __call($m, $args = array())
    {
        throw new lmbDbException("Extension '" . get_class($this) . "' does not support method '$m'");
    }
}
