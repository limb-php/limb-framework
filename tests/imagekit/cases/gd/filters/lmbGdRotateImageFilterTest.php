<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\imagekit\cases\gd\filters;

use tests\imagekit\cases\filters\lmbBaseRotateImageFilterTestCase;

/**
 * @package imagekit
 * @version $Id: lmbGdRotateImageFilterTest.php 8065 2010-01-20 04:18:19Z
 */
class lmbGdRotateImageFilterTest extends lmbBaseRotateImageFilterTestCase
{
    protected $driver = 'gd';

    function setUp(): void
    {
        parent::setUp();

        if (!function_exists('imagerotate'))
            $this->markTestSkipped('imagerotate() function does not exist. Test skipped.');
    }
}
