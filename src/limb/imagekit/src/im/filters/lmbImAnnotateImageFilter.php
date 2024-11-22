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
 * Annotate image filter
 * @package imagekit
 * @version $Id: lmbImAnnotateImageFilter.php 7486 2009-01-26 19:13:20Z
 */
class lmbImAnnotateImageFilter extends lmbAbstractImageFilter
{
    protected function _generateAnnotation()
    {
        $text_size = $this->getTextSize();
        $text_angle = (float)$this->getTextAngle();
        $max_box_width = 200;
        $max_box_height = 500;

        $x = $text_size; // left
        $y = $max_box_height; // baseline

        $draw = new \ImagickDraw();
        $draw->setFont($this->getTextFont());
        $draw->setFontSize($text_size);
        $color = new \ImagickPixel("#" . $this->getTextColor());
        $draw->setFillColor($color);

        $img = new \Imagick();
        $img->newImage($max_box_width + 2 * $text_size, 2 * $max_box_height, "none");
        $img->annotateImage($draw, $x, $y, $text_angle, $this->getText());
        $img->trimImage(0);

        return $img;
    }

    function apply(lmbAbstractImageContainer $container)
    {
        if (!$text = $this->getText())
            return;

        $annotated_img = $this->_generateAnnotation();

        $container->getResource()->compositeImage($annotated_img, \Imagick::COMPOSITE_OVER, $this->getX(), $this->getY());

        $annotated_img->destroy();
    }

    function getTextFontPath()
    {
        return $this->getParam('text_font_path', '');
    }

    function getTextFont()
    {
        $font_file_name = $this->getParam('text_font', null);
        return $this->getTextFontPath() . $font_file_name;
    }

    function getTextSize()
    {
        return $this->getParam('text_size', 14);
    }

    function getTextAngle()
    {
        return $this->getParam('text_angle', 0);
    }

    function getTextColor()
    {
        return $this->getParam('text_color', '000000');
    }

    function getText()
    {
        return $this->getParam('text', '');
    }

    function getX()
    {
        return $this->getParam('x', 0);
    }

    function getY()
    {
        return $this->getParam('y', 0);
    }
}
