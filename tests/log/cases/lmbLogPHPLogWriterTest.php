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
use limb\log\lmbLogPHPLogWriter;
use limb\net\lmbUri;
use limb\log\lmbLogEntry;
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
