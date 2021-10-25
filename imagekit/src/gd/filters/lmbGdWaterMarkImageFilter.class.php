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
 * Resize image filter
 * @package imagekit
 * @version $Id: lmbGdWaterMarkImageFilter.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbGdWaterMarkImageFilter extends lmbAbstractImageFilter
{
  function apply(lmbAbstractImageContainer $container)
  {
    if($this->getWaterMark() === null)
      return;

    $source = $container->getResource();
    $width = $container->getWidth();
    $height = $container->getHeight();

    $wm_cont = new lmbGdImageContainer();
    $wm_cont->load($this->getWaterMark());
    $watermark = $wm_cont->getResource();
    list($x, $y) = $this->calcPosition($this->getX(), $this->getY(), $width, $height, $wm_cont->getWidth(), $wm_cont->getHeight(), $this->getXCenter(), $this->getYCenter());

    $alpha = 100 - $this->getOpacity();
    if($alpha == 100)
    {
      $transparent = imagecolortransparent($watermark);
      if ($transparent >= 0)
      {
        $trnprt_color = imagecolorsforindex($watermark, $transparent);
        $trnprt_indx = imagecolorallocate($source, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
        imagefill($source, 0, 0, $trnprt_indx);
        imagecolortransparent($source, $trnprt_indx);
      }

      imagealphablending($watermark, false);
      imagesavealpha($watermark, true);

      imagecopy($source, $watermark, $x, $y, 0, 0, $wm_cont->getWidth(), $wm_cont->getHeight());
    }
    else
    {
      imagecopymerge($source, $watermark, $x, $y, 0, 0, $wm_cont->getWidth(), $wm_cont->getHeight(), $alpha);
    }
  }

  /**
   * Calculate position of a watermark
   *
   * @param int $x x position of watermark
   * @param int $y y position of watermark
   * @param int $width width of a marked image
   * @param int $height height of a marked image
   * @param mixed $wm_width width of a watermark
   * @param mixed $wm_height height of a watermark
   * @return array (x, y)
   */
  function calcPosition($x, $y, $width, $height, $wm_width, $wm_height, $x_center = false, $y_center = false)
  {
    if($x == 'center')
      $x_center = true;
    if($y == 'center')
      $y_center = true;

    if($x_center !== false)
    {
      $x += round(($width - $wm_width) / 2);
    }
    else
    {
      if($x === 'left')
        $x = 0;
      if($x === 'right')
        $x = $width - $wm_width;
      if($wm_width < $width && $x < 0)
        $x += $width;
    }
    if($y_center !== false)
    {
      $y += round(($height - $wm_height) / 2);
    }
    else
    {
      if($y === 'top')
        $y = 0;
      if($y === 'bottom')
        $y = $height - $wm_height;
      if($wm_height < $height && $y < 0)
        $y += $height;
    }
    return array($x, $y);
  }

  function getWaterMark()
  {
    return $this->getParam('water_mark', null);
  }

  function getX()
  {
    return $this->getParam('x', 0);
  }

  function getY()
  {
    return $this->getParam('y', 0);
  }

  function getOpacity()
  {
    return $this->getParam('opacity', 0);
  }

  function getXCenter()
  {
    return $this->getParam('xcenter', false);
  }

  function getYCenter()
  {
    return $this->getParam('ycenter', false);
  }
}
