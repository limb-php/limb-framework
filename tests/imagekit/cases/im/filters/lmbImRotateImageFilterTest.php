<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\imagekit\cases\im\filters;

use limb\imagekit\src\im\filters\lmbImRotateImageFilter;
use tests\imagekit\cases\filters\lmbBaseRotateImageFilterTest;

/**
 * @package imagekit
 * @version $Id: lmbGdCropImageFilterTest.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbImRotateImageFilterTest extends lmbBaseRotateImageFilterTest
{
  protected $driver = 'im';
}