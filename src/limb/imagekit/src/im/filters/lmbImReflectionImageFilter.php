<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\imagekit\src\im\filters;

use limb\imagekit\src\lmbAbstractImageFilter;
use limb\imagekit\src\lmbAbstractImageContainer;

/**
 * Reflection image filter
 * @package imagekit
 * @version $Id$
 */
class lmbImReflectionImageFilter extends lmbAbstractImageFilter
{
  function apply(lmbAbstractImageContainer $container)
  {
    $src_w = $container->getWidth();
    $src_h = $container->getHeight();

    $tr = $this->getTransparency();
    $bg_color = $this->getBgColor();
    $reflection_height = $this->getReflectionHeight();
    if($reflection_height == null)
    {
      $height2width = $this->getHeight2Width();
      $reflection_height = $this->_calcReflectionHeight($src_w, $src_h, $height2width);
    }

    if($reflection_height > 0) //for reflection only
    {
      $im = $container->getResource();

      $des_h = $src_h + $reflection_height;

      /* Clone the image and flip it */
      $reflection = $im->clone();
      $reflection->flipImage();

      /* Create gradient. It will be overlayd on the reflection */
      $gradient = new \Imagick();
      if( $finish_transparency = $this->getFinishTransparency() )
        $gradient_str = "gradient:#" . $bg_color . $finish_transparency . "-#" . $bg_color;
      else
        $gradient_str = "gradient:transparent-#" . $bg_color;
      $gradient->newPseudoImage( $src_w,
                                 $reflection_height,
                                 $gradient_str
                              );

      /* Composite the gradient on the reflection */
      $reflection->compositeImage( $gradient, \imagick::COMPOSITE_OVER, 0, 0 );

      /* Add some opacity */
      if($tr > 0)
        $reflection->setImageOpacity( (100 - $tr) / 100 );

      /* Canvas needs to be large enough to hold the both images */
      $canvas = new \Imagick();
      $canvas->newImage( $src_w, $des_h, new \ImagickPixel("#".$bg_color) );

      /* Composite the original image and the reflection on the canvas */
      $canvas->compositeImage( $im, \imagick::COMPOSITE_OVER, 0, 0 );
      $canvas->compositeImage( $reflection, \imagick::COMPOSITE_OVER,
                               0, $src_h );

      $container->replaceResource($canvas);
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

  function getFinishTransparency()
  {
    return $this->getParam('finish_transparency', 0);
  }

  function getDividerSize()
  {
    return $this->getParam('divider_size', 0);
  }

  function getBgColor()
  {
    return $this->getParam('bg_color', 'FFFFFF');
  }
}
