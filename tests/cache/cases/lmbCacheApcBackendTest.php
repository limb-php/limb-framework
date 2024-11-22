<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\cache\cases;

use limb\cache\src\lmbCacheApcBackend;

class lmbCacheApcBackendTest extends lmbCacheBackendTestCase
{

    function setUp(): void
    {
        if (!extension_loaded('apc'))
            $this->markTestSkipped('APC extension not found. Test skipped.');

        if (!ini_get('apc.enabled'))
            $this->markTestSkipped('APC extension not enabled. Test skipped.');

        if ((!ini_get('apc.enable_cli') and php_sapi_name() == 'cli'))
            $this->markTestSkipped('APC CLI not enabled. Test skipped.');
    }

    function _createPersisterImp()
    {
        return new lmbCacheApcBackend();
    }

    function testAddLock()
    {
        $this->assertTrue($this->cache->set(1, $v = 'value'));

        $this->assertFalse($this->cache->add(1, 'value_add'));
        $this->assertFalse($this->cache->add(1, 'value_add'), 'apc_add() deletes variables on second call, see http://pecl.php.net/bugs/bug.php?id=13735');

        $this->assertEquals($this->cache->get(1), $v, 'original value has been reseted by apc_add()');

        $this->assertTrue($this->cache->add(2, 'value2'));

        $this->cache->set(2, 'new value');
        $this->assertEquals($this->cache->get(2), 'new value');
    }
}
