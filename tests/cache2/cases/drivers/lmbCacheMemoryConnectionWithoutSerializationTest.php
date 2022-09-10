<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

use limb\cache2\src\drivers\lmbCacheMemoryConnection;

class lmbCacheMemoryConnectionWithoutSerializationTest extends lmbCacheMemoryConnectionTest
{
  function __construct()
  {    
    $this->dsn = 'memory:?need_serialization=0';
  }
  
  function testObjectClone()
  {
    // can't work without serilization
  }
  
}
