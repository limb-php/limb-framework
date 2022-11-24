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
 * Grayscale image filter
 * @package imagekit
 * @version $Id: $
 */
class lmbImGrayscaleImageFilter extends lmbAbstractImageFilter
{
  function apply(lmbAbstractImageContainer $container)
  {
    $container->getResource()->setImageType(Imagick :: IMGTYPE_GRAYSCALE);
  }
}
