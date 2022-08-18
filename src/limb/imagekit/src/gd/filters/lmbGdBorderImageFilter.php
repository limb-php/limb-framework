<?php
namespace limb\imagekit\src\gd\filters;

use limb\imagekit\src\lmbAbstractImageFilter;
use limb\imagekit\src\lmbAbstractImageContainer;
use limb\imagekit\src\gd\lmbGdImageContainer;

/**
 * Border image filter
 * @package imagekit
 * @version $Id: lmbGdBorderImageFilter.class.php 7071 2008-06-25 14:33:29Z 3d-max $
 */
class lmbGdBorderImageFilter extends lmbAbstractImageFilter
{
  function apply(lmbAbstractImageContainer $container)
  {
    $im = $container->getResource();
    $color_arr = $this->getColor();
    $color = ImageColorAllocate($im, $color_arr['red'], $color_arr['green'], $color_arr['blue']);

    if($this->getThickness() > 0)
    {
      $x1 = 0;
      $y1 = 0;
      $x2 = $container->getWidth() - 1;
      $y2 = $container->getHeight() - 1;

      for($i = 0; $i < $this->getThickness(); $i++)
        ImageRectangle($im, $x1++, $y1++, $x2--, $y2--, $color);
    }

    if($this->getIsRoundCorner() && $round_corner_png = $this->getRoundCornerPng())
    {
      $rc_cont = new lmbGdImageContainer();
      $rc_cont->load($round_corner_png);
      $rc = $rc_cont->getResource();

      imagealphablending($rc, false);
      imagesavealpha($rc, true);

      $rc_width = $rc_cont->getWidth() / 2;
      $rc_height = $rc_cont->getHeight() / 2;
      imagecopy($im, $rc, 0, 0, 0, 0, $rc_width, $rc_height); //top left
      imagecopy($im, $rc, $container->getWidth() - $rc_width, 0, $rc_width, 0, $rc_width, $rc_height); //top right
      imagecopy($im, $rc, 0, $container->getHeight() - $rc_height, 0, $rc_height, $rc_width, $rc_height); //bottom left
      imagecopy($im, $rc, $container->getWidth() - $rc_width, $container->getHeight() - $rc_height, $rc_width, $rc_height, $rc_width, $rc_height); //bottom right
    }
  }

  function getThickness()
  {
    return $this->getParam('thickness', 1);
  }

  function getColor()
  {
    $color = $this->getParam('color', 'FFFFFF');
    return $this->parseHexColor($color);
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

