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

use limb\core\src\lmbEnv;

class lmbCacheFileConnectionWithoutSerializationTest extends lmbCacheFileConnectionTest
{
  function __construct()
  {
    $dir = lmbEnv::get('LIMB_VAR_DIR') . '/cache';
    $this->dsn = 'file:///' . $dir . '?need_serialization=0';
  }
  
  function testObjectClone()
  {
    // can't work without serilization
  }
  
  function testGet_Positive_FalseValue()
  {
    // can't work without serilization
  }
  
  function testProperSerializing()
  {
    // can't work without serilization
  }
  
  function _getCachedValues()
  {
    return array(
      'some value',
   );
  }
}
