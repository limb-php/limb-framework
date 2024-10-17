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
