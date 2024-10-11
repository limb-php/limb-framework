<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\Tests\Log\Cases\Toolkit;

require(dirname(__FILE__) . '/../.setup.php');

use PHPUnit\Framework\TestCase;
use limb\toolkit\lmbToolkit;
use limb\log\toolkit\lmbLogTools;
use limb\log\lmbLogFirePHPWriter;

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
        $dsnes = $this->toolkit->getConfLogDSNes();
        $this->assertCount(1, $dsnes);
        $this->assertEquals($this->toolkit->getDefaultErrorDsn(), $dsnes['error']);
    }

    function testGetLogDSNes_fromConfig()
    {
        $this->toolkit->setConf('common', array('logs' => array('foo')));

        $dsnes = $this->toolkit->getConfLogDSNes();
        $this->assertEquals('foo', $dsnes[0]);
    }

    function testGetLog()
    {
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $logs_conf = array('logs' =>
            ['db' => 'firePHP://localhost/?check_extension=0']
        );
        $this->toolkit->setConf('common', $logs_conf);

        $writer = current($this->toolkit->getLog('db')->getWriters());
        $this->assertInstanceOf(lmbLogFirePHPWriter::class, $writer);
        $this->assertFalse($writer->isClientExtensionCheckEnabled());
    }
}
