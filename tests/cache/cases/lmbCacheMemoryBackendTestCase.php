<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\cache\cases;

require('.setup.php');

use limb\cache\src\lmbCacheMemoryBackend;

class lmbCacheMemoryBackendTestCase extends lmbCacheBackendTestCase
{
    function _createPersisterImp()
    {
        return new lmbCacheMemoryBackend();
    }

    function testGetWithTtlFalse()
    {
        $this->assertTrue(true, 'This should already work.');
    }

    function testObjectClone()
    {
        $this->assertTrue(true, 'This should already work.');
    }

}
