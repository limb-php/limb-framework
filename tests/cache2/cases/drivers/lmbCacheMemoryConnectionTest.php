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
    function __construct()
    {
        parent::__construct();

        $this->dsn = 'memory:/';
    }

    function testGetWithTtl_differentThread()
    {
        //memory not share between threads

        $this->assertTrue(true);
    }
}
