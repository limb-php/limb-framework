<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\imagekit\cases\gd\filters;

use Tests\imagekit\cases\filters\lmbBaseRotateImageFilterTestCase;

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
