<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\tree\src;

/**
 * @package tree
 * @version $Id: lmbTreeDecorator.php
 */
abstract class lmbTreeDecorator implements lmbTreeInterface
{
    protected $tree;

    public function __construct($tree)
    {
        $this->tree = $tree;
    }
}
