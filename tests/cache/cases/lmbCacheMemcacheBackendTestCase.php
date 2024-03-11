<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\cache\cases;

use limb\cache\src\lmbCacheMemcacheBackend;

class lmbCacheMemcacheBackendTestCase extends lmbCacheBackendTestCase
{

    private $_host = 'localhost';

    private $_port = 11211;

    function setUp(): void
    {
        if (!extension_loaded('memcache'))
            $this->markTestSkipped('Memcache extension not found. Test skipped.');
        if (!class_exists('Memcache'))
            $this->markTestSkipped('Memcache class not found. Test skipped.');
        if (class_exists('Memcache')) {
            $memcache = new \Memcache();
            if (!@$memcache->connect($this->_host, $this->_port))
                $this->markTestSkipped("memcached is not running on $this->_host:$this->_port. Test skipped.");
            @$memcache->close();
        }
    }

    function _createPersisterImp()
    {
        return new lmbCacheMemcacheBackend();
    }

}
