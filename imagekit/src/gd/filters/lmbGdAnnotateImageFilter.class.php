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
 * Annotate image filter
 * @package imagekit
 * @version $Id: lmbGdAnnotateImageFilter.class.php 7486 2009-01-26 19:13:20Z 3d-max $
 */
class lmbGdAnnotateImageFilter extends lmbAbstractImageFilter
{
  function apply(lmbAbstractImageContainer $container)
  {
    if( !$text = $this->getText() )
      return;

    $text_size = $this->getTextSize();
    $x = $this->getX();
    $y = $this->getY() + $text_size;
    $text_color = $this->getTextColor();

    $im = $container->getResource();
    $color = imagecolorallocate($im, $text_color['red'], $text_color['green'], $text_color['blue']);
    imagettftext($im, $text_size, $this->getTextAngle(), $x, $y, $color, $this->getTextFont(), $text);
  }

  function getTextFontPath()
  {
    return $this->getParam('text_font_path', '');
  }

  function getTextFont()
  {
    $font_file_name = $this->getParam('text_font', null);
    return $this->getTextFontPath() . $font_file_name;
  }

  function getTextSize()
  {
    return $this->getParam('text_size', 14);
  }

  function getTextAngle()
  {
    return $this->getParam('text_angle', 0);
  }

  function getTextColor()
  {
    $text_color = $this->getParam('text_color', '000000');
    return $this->parseHexColor($text_color);
  }

  function getText()
  {
    return $this->getParam('text', '');
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

