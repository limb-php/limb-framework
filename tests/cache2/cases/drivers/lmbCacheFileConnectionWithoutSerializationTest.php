<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\cache2\cases\drivers;

require_once(dirname(__FILE__) . '/../.setup.php');

use limb\core\src\lmbEnv;

class lmbCacheFileConnectionWithoutSerializationTest extends lmbCacheFileConnectionTest
{

    function setUp(): void
    {
        $dir = lmbEnv::get('LIMB_VAR_DIR') . '/cache';
        $this->dsn = 'file:///' . $dir . '?need_serialization=0';

        parent::setUp();
    }


    function testObjectClone()
    {
        // can't work without serialization
    }

    function testGet_Positive_FalseValue()
    {
        // can't work without serialization
    }

    function testProperSerializing()
    {
        // can't work without serialization
    }

    function _getCachedValues()
    {
        return array(
            'some value',
        );
    }
}
