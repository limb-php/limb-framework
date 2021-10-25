<?php
namespace limb\imagekit\src\gd\filters;

use limb\imagekit\src\lmbAbstractImageFilter;
use limb\imagekit\src\lmbAbstractImageContainer;

/**
 * SepiaTone image filter
 * @package imagekit
 * @version $Id: lmbGdGrayscaleImageFilter.class.php 7071 2008-06-25 14:33:29Z 3d-max $
 */
class lmbGdSepiaToneImageFilter extends lmbAbstractImageFilter
{
  function apply(lmbAbstractImageContainer $container)
  {
    $gdimg = $container->getResource();
    $ImageSX = $container->getWidth();
    $ImageSY = $container->getHeight();
    $amount = $this->getThreshold();
    $targetColor = $this->getTargetColor();

    /* */
    $amountPct   = $amount / 100;

    if ($amount != 0)
    {
      if (ImageFilter($gdimg, IMG_FILTER_GRAYSCALE))
      {
        $r = round($amountPct * hexdec(substr($targetColor, 0, 2)));
        $g = round($amountPct * hexdec(substr($targetColor, 2, 2)));
        $b = round($amountPct * hexdec(substr($targetColor, 4, 2)));
        if (ImageFilter($gdimg, IMG_FILTER_COLORIZE, $r, $g, $b))
          return;
      }

      $TargetPixel['red']   = hexdec(substr($targetColor, 0, 2));
      $TargetPixel['green'] = hexdec(substr($targetColor, 2, 2));
      $TargetPixel['blue']  = hexdec(substr($targetColor, 4, 2));

      for ($x = 0; $x < $ImageSX; $x++)
      {
        for ($y = 0; $y < $ImageSY; $y++) {
          $OriginalPixel = $this->GetPixelColor($gdimg, $x, $y);
          $GrayPixel = $this->GrayscalePixel($OriginalPixel);

          // http://www.gimpguru.org/Tutorials/SepiaToning/
          // "In the traditional sepia toning process, the tinting occurs most in
          // the mid-tones: the lighter and darker areas appear to be closer to B&W."
          $SepiaAmount = ((128 - abs($GrayPixel['red'] - 128)) / 128) * $amountPct;

          foreach ($TargetPixel as $key => $value) {
            $NewPixel[$key] = round(max(0, min(255, $GrayPixel[$key] * (1 - $SepiaAmount) + ($TargetPixel[$key] * $SepiaAmount))));
          }
          $newColor = ImageColorAllocateAlpha($gdimg, $NewPixel['red'], $NewPixel['green'], $NewPixel['blue'], $OriginalPixel['alpha']);
          ImageSetPixel($gdimg, $x, $y, $newColor);
        }
      }
    }
  }

  function GetPixelColor($img, $x, $y)
  {
    return ImageColorsForIndex($img, ImageColorAt($img, $x, $y));
  }

  function GrayscaleValue($r, $g, $b)
  {
    return round(($r * 0.30) + ($g * 0.59) + ($b * 0.11));
  }

  function GrayscalePixel($OriginalPixel)
  {
    $gray = $this->GrayscaleValue($OriginalPixel['red'], $OriginalPixel['green'], $OriginalPixel['blue']);
    return array('red'=>$gray, 'green'=>$gray, 'blue'=>$gray);
  }

  function getThreshold()
  {
    return $this->getParam('threshold', 50.0);
  }

  function getTargetColor()
  {
    return $this->getParam('target_color', '875023');
  }
}

