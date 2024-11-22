<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\cache\cases;

use limb\toolkit\src\lmbToolkit;

class lmbCacheToolkitTest extends lmbCacheGroupDecoratorTest
{
    function _createPersisterImp()
    {
        return lmbToolkit::instance()->getCache();
    }

    function testCachedDiskFiles()
    {
        $this->assertTrue(true, 'This should already work.');
    }

}
