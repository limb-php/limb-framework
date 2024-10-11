<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\Tests\Log\Cases;

use PHPUnit\Framework\TestCase;
use limb\log\lmbLogFileWriter;
use limb\net\lmbUri;
use limb\log\lmbLogEntry;
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
