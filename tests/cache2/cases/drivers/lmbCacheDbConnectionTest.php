<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\cache2\cases\drivers;

require_once(dirname(__FILE__) . '/../.setup.php');

class lmbCacheDbConnectionTest extends lmbCacheConnectionTestCase
{
    protected $skip = false;

    function setUp(): void
    {
        if ($this->skip)
            $this->markTestSkipped('lmbCacheDbConnection test skipped (no fixture found).');

        $this->dsn = 'db://dsn?table=lmb_cache2';

        parent::setUp();
    }

}
