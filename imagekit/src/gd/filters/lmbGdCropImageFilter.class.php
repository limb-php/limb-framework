<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2008 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/imagekit/src/lmbAbstractImageFilter.class.php');

/**
 * Crop image filter
 * @package imagekit
 * @version $Id: lmbGdCropImageFilter.class.php 8152 2010-03-29 00:04:37Z korchasa $
 */
class lmbGdCropImageFilter extends lmbAbstractImageFilter
{
  function apply(lmbAbstractImageContainer $container)
  {
    if($this->getWidth() == 0 || $this->getHeight() == 0)
      return;

    list($x, $y, $width, $height) = $this->calculateCropArea($container->getWidth(), $container->getHeight());
    $im = $container->isPallete() ? imagecreate($width, $height) : imagecreatetruecolor($width, $height);
    imagecopy($im, $container->getResource(), 0, 0, $x, $y, $width, $height);
    $container->replaceResource($im);
  }

  function calculateCropArea($image_width, $image_height)
  {
    $width = $this->getWidth();
    $height = $this->getHeight();
    if($width === null)
      $width = $image_width;
    if($height === null)
      $height = $image_height;

    $x = $this->getX();
    $y = $this->getY();

    if(is_string($x))
    {
      if($x == "left")
      {
        $x = 0;
      }
      elseif($x == "center")
      {
        $x = ceil(($image_width - $width) / 2);
        if($x < 0)
          $x = 0;
      }
    }

    if(is_string($y))
    {
      if($y == "top")
      {
        $y = 0;
      }
      elseif($y == "center")
      {
        $y = ceil(($image_height - $height) / 2);
        if($y < 0)
          $y = 0;
      }
    }

    if($x + $width > $image_width)
      $width -= $x + $width - $image_width;
    if($y + $height > $image_height)
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
}
