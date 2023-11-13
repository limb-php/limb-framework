<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace Tests\imagekit\cases\gd\filters;

use Tests\imagekit\cases\filters\lmbBaseCropImageFilterTest;

/**
 * @package imagekit
 * @version $Id: lmbGdCropImageFilterTest.php 8065 2010-01-20 04:18:19Z
 */
class lmbGdCropImageFilterTest extends lmbBaseCropImageFilterTest
{
  protected $driver = 'gd';
}