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
 * SepiaTone image filter
 * @package imagekit
 * @version $Id: $
 */
class lmbImSepiaToneImageFilter extends lmbAbstractImageFilter
{
    function apply(lmbAbstractImageContainer $container)
    {
        $threshold = $this->getThreshold();

        $container->getResource()->sepiaToneImage($threshold);
    }

    function getThreshold()
    {
        return $this->getParam('threshold', 80.0);
    }
}
