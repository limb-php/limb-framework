<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\imagekit\cases\im\filters;

use tests\imagekit\cases\filters\lmbBaseCropImageFilterTestCase;

/**
 * @package imagekit
 * @version $Id: lmbImCropImageFilterTest.php 7486 2009-01-26 19:13:20Z
 */
class lmbImCropImageFilterTest extends lmbBaseCropImageFilterTestCase
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
