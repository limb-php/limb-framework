<?php

namespace limb\imagekit\src\gd\filters;

use limb\imagekit\src\lmbAbstractImageFilter;
use limb\imagekit\src\lmbAbstractImageContainer;

/**
 * Grayscale image filter
 * @package imagekit
 * @version $Id: lmbGdGrayscaleImageFilter.php 7071 2008-06-25 14:33:29Z
 */
class lmbGdGrayscaleImageFilter extends lmbAbstractImageFilter
{
    function apply(lmbAbstractImageContainer $container)
    {
        $source = $container->getResource();
        $src_w = $container->getWidth();
        $src_h = $container->getHeight();

        $bwimage = imagecreate($src_w, $src_h);
        for ($c = 0; $c < 256; $c++)
            imagecolorallocate($bwimage, $c, $c, $c);
        imagecopymerge($bwimage, $source, 0, 0, 0, 0, $src_w, $src_h, 100);

        $container->replaceResource($bwimage);
    }
}
