<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
use limb\cache\src\lmbCacheMemoryBackend;

class lmbCacheMemoryBackendTest extends lmbCacheBackendTest
{
  function _createPersisterImp()
  {
    return new lmbCacheMemoryBackend();
  }
  
  function  testGetWithTtlFalse()
  {
    return;
  }
  
  function testObjectClone()
  {
    return;
  }
  
}
