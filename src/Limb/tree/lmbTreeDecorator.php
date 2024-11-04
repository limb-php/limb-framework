<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\tree;

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
