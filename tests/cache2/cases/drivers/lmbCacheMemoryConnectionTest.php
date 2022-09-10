<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

use limb\cache2\src\drivers\lmbCacheMemoryConnection;

class lmbCacheMemoryConnectionTest extends lmbCacheConnectionTest
{
  function __construct()
  {
    $this->dsn = 'memory:/';
  }
  
  function testGetWithTtl_differentThread()
  {
    //memory not share between threads
  }
}
