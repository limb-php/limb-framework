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
use limb\log\lmbLogFirePHPWriter;
use limb\toolkit\lmbToolkit;
use limb\log\lmbLogEntry;
use limb\net\toolkit\lmbNetTools;
use limb\net\lmbUri;
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

        $this->assertMatchesRegularExpression('/Error/', $headers[4]);
        $this->assertMatchesRegularExpression('/foo/', $headers[4]);
    }
}
