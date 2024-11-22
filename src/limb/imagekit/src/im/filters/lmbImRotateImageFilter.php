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
