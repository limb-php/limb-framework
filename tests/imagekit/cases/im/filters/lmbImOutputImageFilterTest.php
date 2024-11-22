<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\imagekit\cases\im\filters;

use tests\imagekit\cases\filters\lmbBaseOutputImageFilterTestCase;

/**
 * @package imagekit
 * @version $Id: lmbGdCropImageFilterTest.php 7486 2009-01-26 19:13:20Z
 */
class lmbImOutputImageFilterTest extends lmbBaseOutputImageFilterTestCase
{
    protected $driver = 'im';

    function setUp(): void
    {
        if (!extension_loaded('imagick')) {
            $this->markTestSkipped("Imagick library tests are skipped since Imagick extension is disabled.");
        }

        parent::setUp();
    }
}
