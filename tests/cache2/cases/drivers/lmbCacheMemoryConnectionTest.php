<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\cache2\cases\drivers;

class lmbCacheMemoryConnectionTest extends lmbCacheConnectionTestCase
{
  function __construct()
  {
      parent::__construct();

      $this->dsn = 'memory:/';
  }
  
  function testGetWithTtl_differentThread()
  {
      //memory not share between threads

      $this->assertTrue(true);
  }
}
