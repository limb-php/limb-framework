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
 * Negate image filter
 * @package imagekit
 * @version $Id: $
 */
class lmbGdNegateImageFilter extends lmbAbstractImageFilter
{
    function apply(lmbAbstractImageContainer $container)
    {
        $image = $container->getResource();

        if (function_exists('imagefilter')) {
            imagefilter($image, IMG_FILTER_NEGATE);
            return;
        }

        for ($x = 0; $x < imagesx($image); ++$x) {
            for ($y = 0; $y < imagesy($image); ++$y) {
                $index = imagecolorat($image, $x, $y);
                $rgb = imagecolorsforindex($image, $index);
                $color = imagecolorallocate($image, 255 - $rgb['red'], 255 - $rgb['green'], 255 - $rgb['blue']);

                imagesetpixel($image, $x, $y, $color);
            }
        }
    }
}
