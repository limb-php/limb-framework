<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\cache2\cases\drivers;

require_once('tests/dbal/cases/init.inc.php');

class lmbCacheDbConnectionTest extends lmbCacheConnectionTest
{
  protected $storage_init_file = 'limb/dbal/common.inc.php';
  protected $skip = false;

  function setUp(): void
  {
      if( $this->skip )
          $this->markTestSkipped('lmbCacheDbConnection test skipped (no fixture found).');
  }

  function __construct()
  {
    lmb_tests_init_db_dsn();

    $this->dsn = 'db://dsn?table=lmb_cache2';
    $this->fixture_path = dirname(__FILE__) . '/../../../init/cache.';
    $this->skip = lmb_tests_db_dump_does_not_exist($this->fixture_path, 'CACHE2');

    if(!$this->skip)
      lmb_tests_setup_db($this->fixture_path);

    parent::__construct();
  }

  function __destruct()
  {
    if(!$this->skip)
      lmb_tests_teardown_db();
  }
}
