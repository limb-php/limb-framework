<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2008 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\imagekit\gd\filters;

use limb\imagekit\lmbAbstractImageFilter;
use limb\imagekit\lmbAbstractImageContainer;

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
