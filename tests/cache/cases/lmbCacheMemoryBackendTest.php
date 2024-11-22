<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\cache\cases;

use limb\cache\src\lmbCacheMemoryBackend;

class lmbCacheMemoryBackendTest extends lmbCacheBackendTestCase
{
    function _createPersisterImp()
    {
        return new lmbCacheMemoryBackend();
    }

//    function testGetWithTtlFalse()
//    {
//        $this->assertTrue(true, 'This should already work.');
//    }
//
//    function testObjectClone()
//    {
//        $this->assertTrue(true, 'This should already work.');
//    }

}
