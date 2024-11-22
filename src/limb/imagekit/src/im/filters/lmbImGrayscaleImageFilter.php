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
 * Grayscale image filter
 * @package imagekit
 * @version $Id: $
 */
class lmbImGrayscaleImageFilter extends lmbAbstractImageFilter
{
    function apply(lmbAbstractImageContainer $container)
    {
        $container->getResource()->setImageType(\Imagick::IMGTYPE_GRAYSCALE);
    }
}
