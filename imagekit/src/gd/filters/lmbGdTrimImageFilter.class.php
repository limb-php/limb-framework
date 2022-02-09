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
use limb\imagekit\src\lmbAbstractImageContainer;

/**
 * Trim image filter
 * @package imagekit
 * @version $Id: $
 */
class lmbGdEdgeImageFilter extends lmbAbstractImageFilter
{
  function apply(lmbAbstractImageContainer $container)
  {
    if( $this->getTrim() === false )
      return;

    $image = $container->getResource();

    $bwimage = imagecropauto($image, $mode = IMG_CROP_DEFAULT, $threshold = 0.5, $color = -1);

    $container->replaceResource($bwimage);

    //imagedestroy($image);
  }

  function getTrim()
  {
    return $this->getParam('trim', false);
  }
}
