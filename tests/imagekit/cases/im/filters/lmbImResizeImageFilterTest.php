<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace Tests\imagekit\cases\im\filters;

use Tests\imagekit\cases\filters\lmbBaseResizeImageFilterTest;

/**
 * @package imagekit
 * @version $Id: lmbGdCropImageFilterTest.php 7486 2009-01-26 19:13:20Z
 */
class lmbImResizeImageFilterTest extends lmbBaseResizeImageFilterTest
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
