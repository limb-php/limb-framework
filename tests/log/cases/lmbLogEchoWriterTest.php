<?php
/*
 *
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\log\cases;

use PHPUnit\Framework\TestCase;
use limb\log\src\lmbLogEchoWriter;
use limb\net\src\lmbUri;
use limb\log\src\lmbLogEntry;
use Psr\Log\LogLevel;

class lmbLogEchoWriterTest extends TestCase
{

    function testWrite()
    {
        $writer = new lmbLogEchoWriter(new lmbUri());
        ob_start();
        $writer->write(new lmbLogEntry(LogLevel::ERROR, 'foo'));
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertMatchesRegularExpression('/Error/', $output);
        $this->assertMatchesRegularExpression('/foo/', $output);
    }
}
