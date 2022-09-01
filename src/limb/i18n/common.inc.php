<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package i18n
 * @version $Id: common.inc.php 8042 2010-01-19 20:53:10Z korchasa $
 */
require_once(dirname(__FILE__) . '/../core/common.inc.php');
require_once(dirname(__FILE__) . '/../fs/common.inc.php');

use limb\toolkit\src\lmbToolkit;

function lmb_i18n($text, $arg1 = null, $arg2 = null)
{
  $toolkit = lmbToolkit::instance();

  return $toolkit->translate($text, $arg1, $arg2);
}
