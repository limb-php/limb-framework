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
use limb\log\src\lmbLog;
use limb\log\src\lmbLogWriterInterface;
use limb\net\src\lmbUri;
use limb\core\src\exception\lmbException;
use limb\log\src\lmbLogEntry;
use Psr\Log\LogLevel;

class lmbLogTest extends TestCase
{

    /**
     * @var lmbLog
     */
    protected $log;

    function setUp(): void
    {
        $this->log = new lmbLog();
        $this->log->registerWriter(new lmbLogWriterForLogTests(new lmbUri()));
    }

    function testWritersManipulation()
    {
        $log = new lmbLog();
        $this->assertEquals([], $log->getWriters());

        $log->registerWriter($writer = new lmbLogWriterForLogTests(new lmbUri()));
        $this->assertEquals([0 => $writer], $log->getWriters());

        $log->resetWriters();
        $this->assertEquals([], $log->getWriters());
    }

    function testLogLogInfo()
    {
        $this->log->log(LogLevel::INFO, 'imessage', 'iparam', 'ibacktrace');
        $this->log->log(LogLevel::WARNING, 'imessage2', 'iparam2', 'ibacktrace2');

        $this->assertTrue($this->_getLastLogEntry()->isLevel(LogLevel::WARNING));
        $this->assertEquals('imessage2', $this->_getLastLogEntry()->getMessage());
        $this->assertEquals('iparam2', $this->_getLastLogEntry()->getParams());
        $this->assertEquals('ibacktrace2', $this->_getLastLogEntry()->getBacktrace());
    }

    function testLogException()
    {
        $this->log->logException(new lmbException('exmessage', $code = 42));
        $entry = current($this->log->getWriters())->getWritten();

        $this->assertTrue($entry->isLevel(LogLevel::ERROR));
        $this->assertEquals('exmessage', $entry->getMessage());
    }

    function testSetNotifyLevel()
    {
        $this->log->setNotifyLevel(LogLevel::WARNING);
        $this->log->info('info');
        $this->log->debug('notice');

        $this->assertNull($this->_getLastLogEntry());
    }

    function testSetBacktraceDepth()
    {
        $this->log->setBacktraceDepth(LogLevel::NOTICE, $depth = 0);

        $this->log->log(LogLevel::ALERT, 'info');
        $this->assertCount($this->log->getBacktraceDepth(LogLevel::ALERT), $this->_getLastLogEntry()->getBacktrace()->get());

        $this->log->log(LogLevel::NOTICE, 'notice');
        $this->assertCount($depth, $this->_getLastLogEntry()->getBacktrace()->get());
    }

    function testNotifyLevels()
    {
        $log = new lmbLog();
        $writer = new lmbLogWriterForLogTests(new lmbUri());
        $log->registerWriter($writer);

        $log->warning('test warning message');
        $entry = current($log->getWriters())->getWritten();
        $this->assertEquals('Warning message: test warning message', $entry->asText());

        $log->notice('test notice message');
        $entry = current($log->getWriters())->getWritten();
        $this->assertEquals('Notice message: test notice message', $entry->asText());

        // NO debug log!
        $log->debug('test debug message');
        $entry = current($log->getWriters())->getWritten();
        $this->assertEquals('Notice message: test notice message', $entry->asText());
    }

    /**
     * @return lmbLogEntry
     */
    protected function _getLastLogEntry()
    {
        $currentWriter = current($this->log->getWriters());
        return $currentWriter->getWritten();
    }
}

class lmbLogWriterForLogTests implements lmbLogWriterInterface
{

    protected $entry;

    function __construct(lmbUri $dsn)
    {
    }

    function write(lmbLogEntry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * @return lmbLogEntry
     */
    function getWritten()
    {
        return $this->entry;
    }
}
