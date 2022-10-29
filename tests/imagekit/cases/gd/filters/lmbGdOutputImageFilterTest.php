<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\imagekit\cases\gd\filters;

use limb\imagekit\src\gd\filters\lmbGdOutputImageFilter;
use tests\imagekit\cases\filters\lmbBaseOutputImageFilterTest;

/**
 * @package imagekit
 * @version $Id: lmbGdOutputImageFilterTest.class.php 8065 2010-01-20 04:18:19Z korchasa $
 */
class lmbGdOutputImageFilterTest extends lmbBaseOutputImageFilterTest
{
  protected $driver = 'gd';
}