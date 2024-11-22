<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\imagekit\src\im\filters;

use limb\imagekit\src\lmbAbstractImageFilter;
use limb\imagekit\src\lmbAbstractImageContainer;

/**
 * Trim image filter
 * @package imagekit
 * @version $Id: $
 */
class lmbImTrimImageFilter extends lmbAbstractImageFilter
{
    function apply(lmbAbstractImageContainer $container)
    {
        if ($this->getTrim() === false)
            return;

        $container->getResource()->trimImage($fuzz = 0);
        $container->getResource()->setImagePage(0, 0, 0, 0);
    }

    function getTrim()
    {
        return $this->getParam('trim', false);
    }
}
