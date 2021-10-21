<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2008 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\imagekit\src\gd\filters;

use limb\imagekit\src\lmbAbstractImageFilter;

/**
 * Negate image filter
 * @package imagekit
 * @version $Id: $
 */
class lmbGdNegateImageFilter extends lmbAbstractImageFilter
{
  function apply(lmbAbstractImageContainer $container)
  {
    $image = $container->getResource();

    if(function_exists('imagefilter'))
    {
      imagefilter($image, IMG_FILTER_NEGATE);
      return;
    }

    for($x = 0; $x < imagesx($image); ++$x)
    {
      for($y = 0; $y < imagesy($image); ++$y)
      {
        $index = imagecolorat($image, $x, $y);
        $rgb = imagecolorsforindex($index);
        $color = imagecolorallocate($image, 255 - $rgb['red'], 255 - $rgb['green'], 255 - $rgb['blue']);

        imagesetpixel($im, $x, $y, $color);
      }
    }
  }
}
