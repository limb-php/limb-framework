<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\imagekit\gd\filters;

use limb\imagekit\lmbAbstractImageFilter;
use limb\imagekit\lmbAbstractImageContainer;

/**
 * Resize image filter
 * @package imagekit
 * @version $Id: lmbGdResizeImageFilter.php 7486 2009-01-26 19:13:20Z
 */
class lmbGdResizeImageFilter extends lmbAbstractImageFilter
{
    function apply(lmbAbstractImageContainer $container)
    {
        $src_w = $container->getWidth();
        $src_h = $container->getHeight();
        list($dst_w, $dst_h) = $this->calcNewSize($src_w, $src_h);
        $im = imagecreatetruecolor($dst_w, $dst_h);

        if ($this->getFixAlpha()) {
            $transparent = imagecolortransparent($container->getResource());
            if ($transparent >= 0) {
                $trnprt_color = imagecolorsforindex($container->getResource(), $transparent);
                $trnprt_indx = imagecolorallocate($im, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
                imagefill($im, 0, 0, $trnprt_indx);
                imagecolortransparent($im, $trnprt_indx);
            }

            imagealphablending($im, false);
            imagesavealpha($im, true);
        }

        imagecopyresampled($im, $container->getResource(), 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
        $container->replaceResource($im);
    }

    protected function calcNewSize($src_w, $src_h)
    {
        $dst_w = $this->getWidth();
        if (!$dst_w)
            $dst_w = $src_w;
        $dst_h = $this->getHeight();
        if (!$dst_h)
            $dst_h = $src_h;

        return $this->calcSize($src_w, $src_h, $dst_w, $dst_h, $this->getPreserveAspectRatio(), $this->getSaveMinSize());
    }

    function getWidth()
    {
        return $this->getParam('width');
    }

    function getHeight()
    {
        return $this->getParam('height');
    }

    function getPreserveAspectRatio()
    {
        return $this->getParam('preserve_aspect_ratio', true);
    }

    function getSaveMinSize()
    {
        return $this->getParam('save_min_size', false);
    }

    function getFixAlpha()
    {
        return $this->getParam('fix_alpha', false);
    }
}
