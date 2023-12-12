<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\cache2\cases\drivers;

require_once(dirname(__FILE__) . '/../.setup.php');

class lmbCacheDbConnectionTest extends lmbCacheConnectionTestCase
{
    protected $storage_init_file = 'src/limb/dbal/common.inc.php';
    protected $skip = false;

    function __construct()
    {
        parent::__construct();

        $this->dsn = 'db://dsn?table=lmb_cache2';
    }

    function setUp(): void
    {
        if ($this->skip)
            $this->markTestSkipped('lmbCacheDbConnection test skipped (no fixture found).');

    }


}
