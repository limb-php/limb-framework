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
 * Base class for crop image filter
 * @package imagekit
 * @version $Id:$
 */
abstract class lmbBaseCropImageFilter extends lmbAbstractImageFilter
{
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
    if(is_string($x) && $x == "center")
    {
      $x = ceil(($image_width - $width) / 2);
      if($x < 0)
        $x = 0;
    }
    if(is_string($y) && $y == "center")
    {
      $y = ceil(($image_height - $height) / 2);
      if($y < 0)
        $y = 0;
    }

    if($x + $width > $image_width)
      $width -= $x + $width - $image_width;
    if($y + $height > $image_height)
      $height -= $y + $height - $image_height;

    return array($x, $y, $width, $height);
  }

  function getWidth()
  {
    return $this->getParam('width', null);
  }

  function getHeight()
  {
    return $this->getParam('height', null);
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