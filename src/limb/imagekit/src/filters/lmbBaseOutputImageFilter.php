<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\imagekit\src\filters;

use limb\imagekit\src\lmbAbstractImageFilter;
use limb\imagekit\src\lmbAbstractImageContainer;

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
