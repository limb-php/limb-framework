<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\cache2\cases\drivers;

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
      if( $this->skip )
          $this->markTestSkipped('lmbCacheDbConnection test skipped (no fixture found).');

      \lmb_tests_init_db_dsn();

      $this->fixture_path = dirname(__FILE__) . '/../../../init/cache.';
      $this->skip = \lmb_tests_db_dump_does_not_exist($this->fixture_path, 'CACHE2');

      if(!$this->skip)
          \lmb_tests_setup_db($this->fixture_path);
  }

  function tearDown(): void
  {
    if(!$this->skip)
      \lmb_tests_teardown_db();
  }
}
