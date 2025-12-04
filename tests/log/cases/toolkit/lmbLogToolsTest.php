<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\log\cases\toolkit;

require(dirname(__FILE__) . '/../.setup.php');

use limb\log\src\exception\lmbClassNotFoundException;
use limb\log\src\exception\lmbConfigurationNotFoundException;
use PHPUnit\Framework\TestCase;
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
        $conf = $this->toolkit->getLogConfs();

        $this->assertCount(1, $conf);
        $this->assertEquals($conf['error'], $this->toolkit->getDefaultErrorDsn());
    }

    function testGetLogDSNes_fromConfig()
    {
        $this->toolkit->setConf('common', array(
            'logs' => array(
                'foo'
            ))
        );

        $conf = $this->toolkit->getLogConfs();
        $this->assertEquals('foo', $conf[0]);
    }

    function testGetLog_notexist()
    {
        try {
            $log_conf = $this->toolkit->getLog('notexist');
        } catch (lmbConfigurationNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

}
