<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\tree\src\exception;

/**
 * class lmbTreeInvalidNodeException.
 *
 * @package tree
 * @version $Id$
 */
class lmbTreeInvalidNodeException extends lmbTreeException
{
    protected $node;

    function __construct($node)
    {
        $this->node = $node;
        parent::__construct("Node '{$node}' is invalid");
    }

    function getNode()
    {
        return $this->node;
    }
}


