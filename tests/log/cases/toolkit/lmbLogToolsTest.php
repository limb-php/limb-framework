<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once('./../../../../src/limb/log/toolkit.inc.php');

use PHPUnit\Framework\TestCase;
use limb\core\src\lmbEnv;
use limb\toolkit\src\lmbToolkit;
use limb\log\src\toolkit\lmbLogTools;

class lmbLogToolsTest extends TestCase
{
  protected $toolkit;

  function setUp(): void
  {
    lmbToolkit::save();
    $this->toolkit = lmbToolkit::merge(new lmbLogTools());
  }

  function tearDown(): void
  {
    lmbToolkit::restore();
  }

  function testGetLogDSNes_default()
  {
    $dsnes = $this->toolkit->getLogDSNes();
    $this->assertEquals('file://'.realpath(lmbEnv::get('LIMB_VAR_DIR')).'/log/error.log', $dsnes[0]);
  }

  function testGetLogDSNes_fromConfig()
  {
    $this->toolkit->setConf('common', array('logs' => array('foo')));

    $dsnes = $this->toolkit->getLogDSNes();
    $this->assertEquals('foo', $dsnes[0]);
  }

  function testGetLog()
  {
    $logs_conf = array('logs' => array('firePHP://localhost/?check_extension=0'));
    $this->toolkit->setConf('common', $logs_conf);

    $writer = current($this->toolkit->getLog()->getWriters());
    $this->assertIsA($writer, 'lmbLogFirePHPWriter');
    $this->assertFalse($writer->isClientExtensionCheckEnabled());
  }
}