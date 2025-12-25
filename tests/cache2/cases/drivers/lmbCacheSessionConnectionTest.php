<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\cache2\cases\drivers;

require_once(dirname(__FILE__) . '/../.setup.php');

class lmbCacheSessionConnectionTest extends lmbCacheConnectionTestCase
{

    function setUp(): void
    {
        $this->dsn = 'session:';

        parent::setUp();
    }


    function testGetWithTtl_differentThread()
    {
        //session not share between threads
    }
}
