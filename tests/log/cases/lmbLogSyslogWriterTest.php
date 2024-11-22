<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\log\cases;

use PHPUnit\Framework\TestCase;
use limb\log\src\lmbLogSyslogWriter;
use limb\net\src\lmbUri;
use limb\log\src\lmbLogEntry;
use Psr\Log\LogLevel;

class lmbLogSyslogWriterTest extends TestCase
{

    protected function setUp(): void
    {
        $log_exists = file_exists('/var/log/syslog');
        if (!$log_exists)
            $this->markTestSkipped('Syslog writer test skipped, because /var/log/syslog not found');
    }

    function testWrite()
    {
        $writer = new lmbLogSyslogWriter(new lmbUri());
        $writer->write(new lmbLogEntry(LogLevel::ERROR, "foo\nbar"));
        $content = file_get_contents('/var/log/syslog');
        $this->assertMatchesRegularExpression('/Error/', $content);
        $this->assertMatchesRegularExpression('/foo/', $content);
    }
}
