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
use limb\log\src\lmbLogFirePHPWriter;
use limb\toolkit\src\lmbToolkit;
use limb\net\src\lmbHttpResponse;
use limb\log\src\lmbLogEntry;
use limb\net\src\toolkit\lmbNetTools;
use limb\net\src\lmbUri;

class lmbLogFirePHPWriterTest extends TestCase
{

  function testWrite()
  {
    lmbToolkit::merge(new lmbNetTools());
    lmbToolkit::instance()->setResponse(new lmbHttpResponseForLogTest());
    $writer = new lmbLogFirePHPWriter(new lmbUri('firePHP://localhost/?check_extension=0'));
    $writer->write(new lmbLogEntry(LOG_ERR, 'foo'));
    $headers = lmbToolkit::instance()->getResponse()->getHeaders();
    $this->assertMatchesRegularExpression('/Error/', $headers[4]);
    $this->assertMatchesRegularExpression('/foo/', $headers[4]);
  }
}

class lmbHttpResponseForLogTest extends lmbHttpResponse
{
    function getHeaders()
    {
        return $this->headers;
    }
}
