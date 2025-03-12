<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\log\cases;

use PHPUnit\Framework\TestCase;
use limb\log\src\lmbLogFirePHPWriter;
use limb\toolkit\src\lmbToolkit;
use limb\log\src\lmbLogEntry;
use limb\net\src\toolkit\lmbNetTools;
use limb\net\src\lmbUri;
use Psr\Log\LogLevel;

class lmbLogFirePHPWriterTest extends TestCase
{

    function testWrite()
    {
        lmbToolkit::merge(new lmbNetTools());

        ob_start();

        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $writer = new lmbLogFirePHPWriter(new lmbUri('firePHP://localhost/?check_extension=0'));
        $result = $writer->write(new lmbLogEntry(LogLevel::ERROR, 'foo'));

        $headers = headers_list();

        ob_end_clean();

        $this->assertMatchesRegularExpression('/Error/', $headers[4] ?? '');
        $this->assertMatchesRegularExpression('/foo/', $headers[4] ?? '');
    }
}
