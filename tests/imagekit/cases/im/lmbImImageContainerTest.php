<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\imagekit\cases\im;

use tests\imagekit\cases\lmbBaseImageContainerTestCase;

class lmbImImageContainerTest extends lmbBaseImageContainerTestCase
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
