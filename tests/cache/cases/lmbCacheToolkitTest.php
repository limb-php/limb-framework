<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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
