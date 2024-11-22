<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\log\cases;

use PHPUnit\Framework\TestCase;
use limb\log\src\lmbLogFileWriter;
use limb\net\src\lmbUri;
use limb\log\src\lmbLogEntry;
use Psr\Log\LogLevel;

class lmbLogFileWriterTest extends TestCase
{

    function testWrite()
    {
        $dsn = new lmbUri('file://' . lmb_var_dir() . '/log/error' . uniqid() . '.log');
        $writer = new lmbLogFileWriter($dsn);

        $entry = new lmbLogEntry(LogLevel::ERROR, 'foo');
        $writer->write($entry);

        $content = file_get_contents($writer->getLogFile());
        $this->assertMatchesRegularExpression('/Error/', $content);
        $this->assertMatchesRegularExpression('/foo/', $content);
    }
}
