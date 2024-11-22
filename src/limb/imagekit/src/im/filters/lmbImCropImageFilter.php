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
 * Crop image filter
 * @package imagekit
 * @version $Id: $
 */
class lmbImCropImageFilter extends lmbAbstractImageFilter
{
    function apply(lmbAbstractImageContainer $container)
    {
        if ($this->getWidth() === 0 || $this->getHeight() === 0)
            return;

        list($x, $y, $width, $height) = $this->calculateCropArea($container->getWidth(), $container->getHeight());

        $container->getResource()->cropImage($width, $height, $x, $y);

        if ($this->getTrim())
            $container->getResource()->trimImage();
    }

    function calculateCropArea($image_width, $image_height)
    {
        $x = $this->getX();
        $y = $this->getY();
        $width = $this->getWidth();
        $height = $this->getHeight();
        if ($width === null) {
            $width = $image_width;
        }
        if ($height === null) {
            $height = $image_height;
        }

        if (is_string($x)) {
            if ($x == "left") {
                $x = 0;
            } elseif ($x == "center") {
                $x = ceil(($image_width - $width) / 2);
                if ($x < 0)
                    $x = 0;
            }
        }

        if (is_string($y)) {
            if ($y == "top") {
                $y = 0;
            } elseif ($y == "center") {
                $y = ceil(($image_height - $height) / 2);
                if ($y < 0)
                    $y = 0;
            }
        }

        if ($x + $width > $image_width)
            $width -= $x + $width - $image_width;
        if ($y + $height > $image_height)
            $height -= $y + $height - $image_height;

        return array($x, $y, $width, $height);
    }

    function getWidth()
    {
        return $this->getParam('width');
    }

    function getHeight()
    {
        return $this->getParam('height');
    }

    function getX()
    {
        return $this->getParam('x', 0);
    }

    function getY()
    {
        return $this->getParam('y', 0);
    }

    function getTrim()
    {
        return $this->getParam('trim', 0);
    }
}
