<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\imagekit\src\gd\filters;

use limb\imagekit\src\lmbAbstractImageFilter;
use limb\imagekit\src\lmbAbstractImageContainer;

/**
 * Rotate image filter
 * @package imagekit
 * @version $Id: lmbGdRotateImageFilter.php 7486 2009-01-26 19:13:20Z
 */
class lmbGdRotateImageFilter extends lmbAbstractImageFilter
{
  function apply(lmbAbstractImageContainer $container)
  {
    $angle = $this->getAngle();
    $flip_x = $this->getFlipX();
    $flip_y = $this->getFlipY();

    if($angle || $flip_x || $flip_y)
    {
      if($flip_x && $flip_y)
      {
        $angle += 180;
        $flip_x = false;
        $flip_y = false;
      }

      if($flip_x)
      {
        $cur_im = $container->getResource();

        $src_w = $container->getWidth();
        $src_h = $container->getHeight();
        $im = imagecreatetruecolor($src_w, $src_h);

        imagecopyresampled($im, $cur_im, 0, 0, ($src_w - 1), 0, $src_w, $src_h, -$src_w, $src_h);

        $container->replaceResource($im);
      }

      if($flip_y)
      {
        $cur_im = $container->getResource();

        $src_w = $container->getWidth();
        $src_h = $container->getHeight();
        $im = imagecreatetruecolor($src_w, $src_h);

        imagecopyresampled($im, $cur_im, 0, 0, 0, ($src_h - 1), $src_w, $src_h, $src_w, -$src_h);

        $container->replaceResource($im);
      }

      if($angle)
      {
        $bgcolor = $this->getBgColor();

        $cur_im = $container->getResource();

        $bg = imagecolorallocate($cur_im, $bgcolor['red'], $bgcolor['green'], $bgcolor['blue']);
        $im = imagerotate($cur_im, -$angle, $bg);

        $container->replaceResource($im);
      }
    }
  }

  function getAngle()
  {
    return $this->getParam('angle', 0);
  }

  function getFlipX()
  {
    return $this->getParam('flip_x', false);
  }

  function getFlipY()
  {
    return $this->getParam('flip_y', false);
  }

  function getBgColor()
  {
    $bgcolor = $this->getParam('bgcolor', 'FFFFFF');
    return $this->parseHexColor($bgcolor);
  }
}
