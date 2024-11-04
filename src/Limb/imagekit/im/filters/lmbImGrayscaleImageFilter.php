<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2008 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\imagekit\im\filters;

use limb\imagekit\lmbAbstractImageFilter;
use limb\imagekit\lmbAbstractImageContainer;

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
