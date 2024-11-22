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
 * Border image filter
 * @package imagekit
 * @version $Id: $
 */
class lmbImBorderImageFilter extends lmbAbstractImageFilter
{
    function apply(lmbAbstractImageContainer $container)
    {
        $im = $container->getResource();
        $color = $this->getColor();

        if ($this->getThickness() > 0) {
            $im->borderImage("#" . $color, $this->getThickness(), $this->getThickness());
        }

        if ($this->getIsRoundCorner() && $round_corner_png = $this->getRoundCornerPng()) {
            $rc = new \Imagick();
            $rc->readImage($this->getRoundCornerPng());
            $rc_width = $rc->getImageWidth() / 2;
            $rc_height = $rc->getImageHeight() / 2;

            $rc_clone = $rc->clone();
            $rc_clone->cropImage($rc_width, $rc_height, 0, 0);
            $im->compositeImage($rc_clone, \Imagick::COMPOSITE_OVER, 0, 0); //top left

            $rc_clone = $rc->clone();
            $rc_clone->cropImage($rc_width, $rc_height, $rc_width, 0);
            $im->compositeImage($rc_clone, \Imagick::COMPOSITE_OVER, $container->getWidth() - $rc_width, 0); //top right

            $rc_clone = $rc->clone();
            $rc_clone->cropImage($rc_width, $rc_height, 0, $rc_height);
            $im->compositeImage($rc_clone, \Imagick::COMPOSITE_OVER, 0, $container->getHeight() - $rc_height); //bottom left

            $rc_clone = $rc->clone();
            $rc_clone->cropImage($rc_width, $rc_height, $rc_width, $rc_height);
            $im->compositeImage($rc_clone, \Imagick::COMPOSITE_OVER, $container->getWidth() - $rc_width, $container->getHeight() - $rc_height); //bottom right
        }
    }

    function getThickness()
    {
        return $this->getParam('thickness', 1);
    }

    function getColor()
    {
        return $color = $this->getParam('color', 'FFFFFF');
    }

    function getIsRoundCorner()
    {
        return $this->getParam('is_round_corner', false);
    }

    function getRoundCornerPng()
    {
        return $this->getParam('round_corner_png', '');
    }
}
