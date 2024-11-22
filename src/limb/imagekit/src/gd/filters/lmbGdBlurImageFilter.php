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
 * Blur image filter
 * @package imagekit
 * @version $Id: $
 */
class lmbGdBlurImageFilter extends lmbAbstractImageFilter
{
    function apply(lmbAbstractImageContainer $container)
    {
        $type = $this->getType();

        $im = $container->getResource();

        if (function_exists('imagefilter')) {
            imagefilter($im, IMG_FILTER_GAUSSIAN_BLUR);
        } elseif (function_exists('imageconvolution')) {
            $gaussian = array(array(1.0, 2.0, 1.0),
                array(2.0, 4.0, 2.0),
                array(1.0, 2.0, 1.0));

            imageconvolution($im, $gaussian, 16, 0);
        }
    }

    function getType()
    {
        return $this->getParam('type', '');
    }
}
