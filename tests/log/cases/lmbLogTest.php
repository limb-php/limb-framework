<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

use PHPUnit\Framework\TestCase;
use limb\log\src\lmbLog;
use limb\log\src\lmbLogWriterInterface;
use limb\net\src\lmbUri;
use limb\core\src\exception\lmbException;
use limb\log\src\lmbLogEntry;

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
    $this->assertEquals(array(), $log->getWriters());

    $log->registerWriter($writer = new lmbLogWriterForLogTests(new lmbUri()));
    $this->assertEquals(array($writer), $log->getWriters());

    $log->resetWriters();
    $this->assertEquals(array(), $log->getWriters());
  }

  function testLog()
  {
    $this->log->log('imessage', LOG_INFO, 'iparam', 'ibacktrace');
    $this->assertTrue($this->_getLastLogEntry()->isLevel(LOG_INFO));
    $this->assertEquals('imessage', $this->_getLastLogEntry()->getMessage());
    $this->assertEquals('iparam', $this->_getLastLogEntry()->getParams());
    $this->assertEquals('ibacktrace', $this->_getLastLogEntry()->getBacktrace());
  }

  function testLogException()
  {
    $this->log->logException(new lmbException('exmessage', $code = 42));

    $entry = current($this->log->getWriters())->getWritten();

    $this->assertTrue($entry->isLevel(LOG_ERR));
    $this->assertEquals('exmessage', $entry->getMessage());
  }

  function testSetErrorLevel()
  {
    $this->log->setErrorLevel(LOG_WARNING);
    $this->log->log('info', LOG_INFO);
    $this->log->log('notice', LOG_NOTICE);
    $this->assertNull($this->_getLastLogEntry());
  }

  function testSetBacktraceDepth()
  {
    $this->log->setBacktraceDepth(LOG_NOTICE, $depth = 0);
    $this->log->log('info', LOG_INFO);
    $this->assertNotEqual($depth, count($this->_getLastLogEntry()->getBacktrace()->get()));
    $this->log->log('notice', LOG_NOTICE);
    $this->assertEquals($depth, count($this->_getLastLogEntry()->getBacktrace()->get()));
  }

  /**
   *@return lmbLogEntry
   */
  protected function _getLastLogEntry()
  {
    return current($this->log->getWriters())->getWritten();
  }
}

class lmbLogWriterForLogTests implements lmbLogWriterInterface
{

    protected $entry;

    function __construct(lmbUri $dsn) {}

    function write(lmbLogEntry $entry)
    {
        $this->entry = $entry;
    }

    /**
     *@return lmbLogEntry
     */
    function getWritten()
    {
        return $this->entry;
    }
}
