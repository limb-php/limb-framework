<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\imagekit\filters;

use limb\imagekit\lmbAbstractImageFilter;
use limb\imagekit\lmbAbstractImageContainer;

/**
 * Base class for output image filter
 * @package imagekit
 * @version $Id:$
 */
abstract class lmbBaseOutputImageFilter extends lmbAbstractImageFilter
{

    function apply(lmbAbstractImageContainer $container)
    {
        $container->setOutputType($this->getType());
    }

    function getType()
    {
        return $this->getParam('type', '');
    }

}
