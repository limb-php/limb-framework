<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\imagekit\src\filters;

use limb\imagekit\src\lmbAbstractImageFilter;

/**
 * Base class for rotate image filter
 * @package imagekit
 * @version $Id:$
 */
abstract class lmbBaseRotateImageFilter extends lmbAbstractImageFilter
{
  function getAngle()
  {
    return $this->getParam('angle', 0);
  }

  function getBgColor()
  {
    $bgcolor = $this->getParam('bgcolor', 'FFFFFF');
    return $this->parseHexColor($bgcolor);
  }
}
