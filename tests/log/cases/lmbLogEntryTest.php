<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\log\cases;

use PHPUnit\Framework\TestCase;
use limb\log\src\lmbLogEntry;
use limb\core\src\lmbBacktrace;
use Psr\Log\LogLevel;

class lmbLogEntryTest extends TestCase
{

    function testGetters()
    {
        $entry = new lmbLogEntry(
            $level = LogLevel::INFO,
            $message = 'some text',
            $params = array('foo' => 42),
            $backtrace = new lmbBacktrace(),
            $time = time()
        );
        $this->assertEquals($level, $entry->getLevel());
        $this->assertEquals($message, $entry->getMessage());
        $this->assertEquals($params, $entry->getParams());
        $this->assertEquals($backtrace, $entry->getBacktrace());
        $this->assertEquals($time, $entry->getTime());
    }

    function testGetLevelForHuman()
    {
        $entry = new lmbLogEntry(LogLevel::ERROR, 'foo');
        $this->assertEquals('Error', $entry->getLevelForHuman());

        $entry = new lmbLogEntry(LogLevel::INFO, 'foo2');
        $this->assertEquals('Info', $entry->getLevelForHuman());

        $entry = new lmbLogEntry(LogLevel::DEBUG, 'foo3');
        $this->assertEquals('Debug', $entry->getLevelForHuman());
    }

    function testIsLevel()
    {
        $entry = new lmbLogEntry(LogLevel::ERROR, 'foo');
        $this->assertTrue($entry->isLevel(LogLevel::ERROR));
        $this->assertFalse($entry->isLevel(LogLevel::INFO));
    }

    function testAsText()
    {
        $entry = new lmbLogEntry(LogLevel::ERROR, 'foo&');
        $this->assertMatchesRegularExpression('/Error/', $entry->asText());
        $this->assertMatchesRegularExpression('/foo&/', $entry->asText());
    }

    function testAsHtml()
    {
        $entry = new lmbLogEntry(LogLevel::ERROR, 'foo&');
        $this->assertMatchesRegularExpression('/Error/', $entry->asHtml());
        $this->assertMatchesRegularExpression('/foo&amp;/', $entry->asHtml());
    }
}