<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\imagekit\src\im\filters;

use limb\imagekit\src\lmbAbstractImageFilter;
use limb\imagekit\src\lmbAbstractImageContainer;

/**
 * Watermark image filter
 * @package imagekit
 * @version $Id$
 */
class lmbImWaterMarkImageFilter extends lmbAbstractImageFilter
{
    function apply(lmbAbstractImageContainer $container)
    {
        if ($this->getWaterMark() === null)
            return;

        $width = $container->getWidth();
        $height = $container->getHeight();

        $wm_cont = new \Imagick();
        $wm_cont->readImage($this->getWaterMark());
        list($x, $y) = $this->calcPosition($this->getX(), $this->getY(), $width, $height, $wm_cont->getImageWidth(), $wm_cont->getImageHeight(), $this->getXCenter(), $this->getYCenter());

        if ($this->getOpacity() > 0) {
            if (method_exists($wm_cont, 'setImageOpacity'))
                $wm_cont->setImageOpacity($this->getOpacity() / 100);
            elseif (method_exists($wm_cont, 'setImageAlpha'))
                $wm_cont->setImageAlpha($this->getOpacity() / 100);
        }

        $container->getResource()->compositeImage($wm_cont, \Imagick::COMPOSITE_OVER, $x, $y, \Imagick::CHANNEL_ALL);
        //$container->getResource()->compositeImage($wm_cont, Imagick::COMPOSITE_DEFAULT, $x, $y);
    }

    function calcPosition($x, $y, $width, $height, $wm_width, $wm_height, $x_center = false, $y_center = false)
    {
        if ($x === 'center')
            $x_center = true;
        if ($y === 'center')
            $y_center = true;

        if ($x_center !== false) {
            $x += round(($width - $wm_width) / 2);
        } else {
            if ($x === 'left')
                $x = 0;
            if ($x === 'right')
                $x = $width - $wm_width;
            if ($wm_width < $width && $x < 0)
                $x += $width;
        }
        if ($y_center !== false) {
            $y += round(($height - $wm_height) / 2);
        } else {
            if ($y === 'top')
                $y = 0;
            if ($y === 'bottom')
                $y = $height - $wm_height;
            if ($wm_height < $height && $y < 0)
                $y += $height;
        }
        return array($x, $y);
    }

    function getWaterMark()
    {
        return $this->getParam('water_mark', null);
    }

    function getX()
    {
        return $this->getParam('x', 0);
    }

    function getY()
    {
        return $this->getParam('y', 0);
    }

    function getOpacity()
    {
        return $this->getParam('opacity', 0);
    }

    function getXCenter()
    {
        return $this->getParam('xcenter', false);
    }

    function getYCenter()
    {
        return $this->getParam('ycenter', false);
    }
}
