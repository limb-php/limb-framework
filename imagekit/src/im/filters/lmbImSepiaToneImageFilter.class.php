<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2008 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\imagekit\src\im\filters;

use limb\imagekit\src\lmbAbstractImageFilter;

/**
 * SepiaTone image filter
 * @package imagekit
 * @version $Id: $
 */
class lmbImSepiaToneImageFilter extends lmbAbstractImageFilter
{
  function apply(lmbAbstractImageContainer $container)
  {
    $threshold = $this->getThreshold();

    $container->getResource()->sepiaToneImage($threshold);
  }

  function getThreshold()
  {
    return $this->getParam('threshold', 80.0);
  }
}
