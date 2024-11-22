<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\web_spider\cases;

use PHPUnit\Framework\TestCase;
use limb\net\src\lmbUri;
use limb\web_spider\src\lmbUriContentReader;

class lmbUriContentReaderTest extends TestCase
{
    function testOpen()
    {
        $uri = $this->createMock(lmbUri::class);
        $reader = new lmbUriContentReader();
        $uri
            ->expects($this->once())
            ->method('toString')
            ->willReturn(dirname(__FILE__) . '/../html/index.html');

        $reader->open($uri);
        $this->assertFalse($reader->getContentType()); // since opening a plain text file not html over http
        $this->assertEquals($reader->getContent(),
            file_get_contents(dirname(__FILE__) . '/../html/index.html'));
    }

    function TODO_testGetLazyContent()
    {
    }
}
