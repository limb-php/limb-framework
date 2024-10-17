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
