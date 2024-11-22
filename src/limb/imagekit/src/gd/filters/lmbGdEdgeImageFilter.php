<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\imagekit\src\gd\filters;

use limb\imagekit\src\lmbAbstractImageFilter;
use limb\imagekit\src\lmbAbstractImageContainer;

/**
 * Edge image filter
 * @package imagekit
 * @version $Id: $
 */
class lmbGdEdgeImageFilter extends lmbAbstractImageFilter
{
    function apply(lmbAbstractImageContainer $container)
    {
        $image = $container->getResource();

        if (function_exists('imagefilter')) {
            imagefilter($image, IMG_FILTER_EDGEDETECT);
            return;
        }
    }
}
