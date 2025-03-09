<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\FilterChain;

/**
 * Interface for filter classes to be used with lmbChain
 *
 * @version $Id: lmbChainInterface.php 7486 2009-01-26 19:13:20Z
 * @package filter_chain
 */
interface lmbChainInterface
{
    /**
     * Runs the filter.
     * Filters should decide whether to pass control to the next filter in the chain or not.
     * @param lmbChain filters chain
     * @return void
     * @see lmbChain::next()
     *
     */
    function run($filter_chain);
}
