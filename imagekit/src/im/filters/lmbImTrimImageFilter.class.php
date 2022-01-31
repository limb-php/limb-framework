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
use limb\imagekit\src\lmbAbstractImageContainer;

/**
 * Trim image filter
 * @package imagekit
 * @version $Id: $
 */
class lmbImTrimImageFilter extends lmbAbstractImageFilter
{
  function apply(lmbAbstractImageContainer $container)
  {
    if( !$this->getTrim() )
      return;

    $container->getResource()->trimImage();
  }

  function getTrim()
  {
    return $this->getParam('trim', 0);
  }
}