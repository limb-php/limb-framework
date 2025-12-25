<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\cache2\cases\drivers;

require_once(dirname(__FILE__) . '/../.setup.php');

class lmbCacheMemoryConnectionTest extends lmbCacheConnectionTestCase
{
    function setUp(): void
    {
        $this->dsn = 'memory:/';

        parent::setUp();
    }


    function testGetWithTtl_differentThread()
    {
        //memory not share between threads

        $this->assertTrue(true);
    }
}
