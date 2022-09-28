<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

use PHPUnit\Framework\TestCase;
use limb\log\src\lmbLogSyslogWriter;
use limb\net\src\lmbUri;
use limb\log\src\lmbLogEntry;

class lmbLogSyslogWriterTest extends TestCase
{

  function skip() {
      $log_exists = file_exists('/var/log/syslog');
      $this->skipIf(!$log_exists, 'Syslog writer test skipped, because /var/log/syslog not found');
  }

  function testWrite()
  {
    $writer = new lmbLogSyslogWriter(new lmbUri());
    $writer->write(new lmbLogEntry(LOG_ERR, "foo\nbar"));
    $content = file_get_contents('/var/log/syslog');
    $this->assertPattern('/Error/', $content);
    $this->assertPattern('/foo/', $content);
  }
}
