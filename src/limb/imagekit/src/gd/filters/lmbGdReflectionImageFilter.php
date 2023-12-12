<?php

namespace limb\imagekit\src\gd\filters;

use limb\imagekit\src\lmbAbstractImageFilter;
use limb\imagekit\src\lmbAbstractImageContainer;

/**
 * Reflection image filter
 * @package imagekit
 * @version $Id: lmbGdReflectionImageFilter.php 7071 2008-06-25 14:33:29Z
 */
class lmbGdReflectionImageFilter extends lmbAbstractImageFilter
{
    function apply(lmbAbstractImageContainer $container)
    {
        $src_w = $container->getWidth();
        $src_h = $container->getHeight();

        $tr = $this->getTransparency();
        $bg_color = $this->getBgColor();
        $reflection_height = $this->getReflectionHeight();
        if ($reflection_height == null) {
            $height2width = $this->getHeight2Width();
            $reflection_height = $this->_calcReflectionHeight($src_w, $src_h, $height2width);
        }

        if ($reflection_height > 0) //for reflection only
        {
            $im = $container->getResource();

            $des_h = $src_h + $reflection_height;

            $li = imagecreatetruecolor($src_w, 1);
            $bgc = imagecolorallocate($li, $bg_color['red'], $bg_color['green'], $bg_color['blue']);
            imagefilledrectangle($li, 0, 0, $src_w, 1, $bgc);
            $bg = imagecreatetruecolor($src_w, $des_h);

            //copy original image
            imagecopy($bg, $im, 0, 0, 0, 0, $src_w, $src_h);

            //flip vertical and copy part of original image -> reflection
            imagecopyresampled($bg, $im, 0, $src_h, 0, ($src_h - 1), $src_w, $reflection_height, $src_w, -$reflection_height);

            //fade reflection
            if ($reflection_height > $src_h)
                $in = 100 / $src_h;
            else
                $in = 100 / $reflection_height;
            for ($i = 0; $i <= $reflection_height; $i++) {
                if ($tr > 100)
                    $tr = 100;

                imagecopymerge($bg, $li, 0, ($src_h + $i), 0, 0, $src_w, 1, $tr);
                $tr += $in;
            }

            //add diveder
            if ($this->getDividerSize())
                imagecopymerge($bg, $li, 0, 0, 0, 0, $src_w, $this->getDividerSize(), 100);

            $container->replaceResource($bg);
        }
    }

    protected function _calcReflectionHeight($width, $height, $height2width)
    {
        return ($width * $height2width) - $height;
    }

    function getReflectionHeight()
    {
        return $this->getParam('reflection_height', null);
    }

    function getHeight2Width()
    {
        return $this->getParam('height2width', null);
    }

    function getTransparency()
    {
        return $this->getParam('transparency', 30);
    }

    function getDividerSize()
    {
        return $this->getParam('divider_size', 0);
    }

    function getBgColor()
    {
        $bg_color = $this->getParam('bg_color', 'FFFFFF');
        return $this->parseHexColor($bg_color);
    }
}
