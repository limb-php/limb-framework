<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\imagekit\im\filters;

use limb\imagekit\lmbAbstractImageFilter;
use limb\imagekit\lmbAbstractImageContainer;

/**
 * Rotate image filter
 * @package imagekit
 * @version $Id$
 */
class lmbImRotateImageFilter extends lmbAbstractImageFilter
{
    function apply(lmbAbstractImageContainer $container)
    {
        $angle = $this->getAngle();
        $flip_x = $this->getFlipX();
        $flip_y = $this->getFlipY();

        if ($angle || $flip_x || $flip_y) {
            if ($flip_x && $flip_y) {
                $angle += 180;
                $flip_x = false;
                $flip_y = false;
            }

            if ($flip_x) {
                $container->getResource()->flopImage();
            }

            if ($flip_y) {
                $container->getResource()->flipImage();
            }

            if ($angle) {
                $bgcolor = "#" . $this->getBgColor();

                $container->getResource()->rotateImage(new \ImagickPixel($bgcolor), $angle);
            }
        }
    }

    function getAngle()
    {
        return $this->getParam('angle', 0);
    }

    function getFlipX()
    {
        return $this->getParam('flip_x', false);
    }

    function getFlipY()
    {
        return $this->getParam('flip_y', false);
    }

    function getBgColor()
    {
        return $this->getParam('bgcolor', 'FFFFFF');
    }
}
