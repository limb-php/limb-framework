<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\imagekit\cases\im\filters;

use tests\imagekit\cases\filters\lmbBaseCropImageFilterTest;

/**
 * @package imagekit
 * @version $Id: lmbImCropImageFilterTest.php 7486 2009-01-26 19:13:20Z
 */
class lmbImCropImageFilterTest extends lmbBaseCropImageFilterTest
{
  protected $driver = 'im';

    function setUp(): void
    {
        if(!extension_loaded('imagick'))
        {
            $this->markTestSkipped("Imagick library tests are skipped since Imagick extension is disabled.");
        }

        parent::setUp();
    }
}
