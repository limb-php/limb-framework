<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\cache2\cases\drivers;

class lmbCacheSessionConnectionTest extends lmbCacheConnectionTest
{
  protected $storage_init_file = 'limb/web_app/common.inc.php';
  
  function __construct()
  {    
    $this->dsn = 'session:';
    parent::__construct();
  }
  
  function testGetWithTtl_differentThread()
  {
    //session not share between threads
  }
}
