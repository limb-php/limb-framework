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
 * Resize image filter
 * @package imagekit
 * @version $Id$
 */
class lmbImResizeImageFilter extends lmbAbstractImageFilter
{
    const RESIZE_TYPE_FIT = 1;
    const RESIZE_TYPE_CUT = 2;

    function apply(lmbAbstractImageContainer $container)
    {
        $src_w = $container->getWidth();
        $src_h = $container->getHeight();
        list($dst_w, $dst_h) = $this->calcNewSize($src_w, $src_h);

        $container->getResource()->thumbnailImage($dst_w, $dst_h, false);
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

    function getXxx()
    {
        return $this->getParam('xxx', false);
    }

}
