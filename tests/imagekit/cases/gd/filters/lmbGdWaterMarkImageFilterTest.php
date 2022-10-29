<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\imagekit\cases\gd\filters;

use limb\imagekit\src\gd\filters\lmbGdWaterMarkImageFilter;
use tests\imagekit\cases\filters\lmbBaseWaterMarkImageFilterTest;

/**
 * @package imagekit
 * @version $Id: lmbGdWaterMarkImageFilterTest.class.php 8065 2010-01-20 04:18:19Z korchasa $
 */
class lmbGdWaterMarkImageFilterTest extends lmbBaseWaterMarkImageFilterTest
{
  protected $driver = 'gd';
}