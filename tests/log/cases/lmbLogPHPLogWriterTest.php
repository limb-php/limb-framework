<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\log\cases;

use PHPUnit\Framework\TestCase;
use limb\log\src\lmbLogPHPLogWriter;
use limb\net\src\lmbUri;
use limb\log\src\lmbLogEntry;
use Psr\Log\LogLevel;

class lmbLogPHPLogWriterTest extends TestCase
{

    function testWrite()
    {
        $php_log = lmb_var_dir() . '/php.log';
        ini_set('error_log', $php_log);

        $writer = new lmbLogPHPLogWriter(new lmbUri());
        $writer->write(new lmbLogEntry(LogLevel::ERROR, 'foo'));

        $content = file_get_contents($php_log);
        $this->assertMatchesRegularExpression('/Error/', $content);
        $this->assertMatchesRegularExpression('/foo/', $content);
    }
}
