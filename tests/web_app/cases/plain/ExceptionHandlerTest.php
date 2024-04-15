<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

namespace tests\web_app\cases\plain;

require_once(dirname(__FILE__) . '/../init.inc.php');

use limb\core\src\exception\lmbException;
use limb\log\src\lmbLog;
use limb\net\src\lmbHttpRequest;
use limb\net\src\lmbUri;
use limb\web_app\src\exception\lmbExceptionHandler;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use tests\log\cases\src\lmbLogWriterForLogTests;

class ExceptionHandlerTest extends TestCase
{
    /**
     * @var lmbLog
     */
    protected $logger;

    /**
     * @var lmbExceptionHandler
     */
    protected $handler;

    function setUp(): void
    {
        $this->logger = new lmbLog();
        $this->logger->registerWriter(new lmbLogWriterForLogTests(new lmbUri()));

        $this->handler = new lmbExceptionHandler(__DIR__ . '/../template/server_error.phtml', $this->logger);
    }

    function testLogException()
    {
        $this->handler->handleException(new lmbException('exmessage', $code = 42), lmbHttpRequest::createFromGlobals());
        $entry = current($this->logger->getWriters())->getWritten();

        $this->assertTrue($entry->isLevel(LogLevel::ERROR));
        $this->assertEquals('exmessage', $entry->getMessage());
    }
}
