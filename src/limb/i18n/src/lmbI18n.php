<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace limb\i18n\src;

use limb\toolkit\src\lmbToolkit;

/**
 * class lmbI18n
 *
 * @package i18n
 * @version $Id$
 */

class lmbI18n
{
  function translate($text, $arg1 = null, $arg2 = null)
  {
    $toolkit = lmbToolkit::instance();

    return $toolkit->translate($text, $arg1, $arg2);
  }
}
