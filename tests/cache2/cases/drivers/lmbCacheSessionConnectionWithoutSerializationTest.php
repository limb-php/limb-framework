<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\cache2\cases\drivers;

require_once(dirname(__FILE__) . '/../.setup.php');

class lmbCacheSessionConnectionWithoutSerializationTest extends lmbCacheSessionConnectionTest
{
    function __construct()
    {
        parent::__construct();

        $this->dsn = 'session:?need_serialization=0';
    }

    function testObjectClone()
    {
        // can't work without serilization
    }
}
