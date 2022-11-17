<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\imagekit\cases\gd\filters;

use tests\imagekit\cases\filters\lmbBaseRotateImageFilterTest;

/**
 * @package imagekit
 * @version $Id: lmbGdRotateImageFilterTest.php 8065 2010-01-20 04:18:19Z korchasa $
 */
class lmbGdRotateImageFilterTest extends lmbBaseRotateImageFilterTest
{
  protected $driver = 'gd';

  function skip()
  {
      if(!function_exists('imagerotate'))
        $this->markTestSkipped('imagerotate() function does not exist. Test skipped.');
  }
}
