<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

use PHPUnit\Framework\TestCase;
use limb\log\src\lmbLogFileWriter;
use limb\net\src\lmbUri;
use limb\log\src\lmbLogEntry;

class lmbLogFileWriterTest extends TestCase {

  function testWrite()
  {
    $dsn = new lmbUri('file://'.lmb_var_dir().'/logs/error'.uniqid().'.log');
    $writer = new lmbLogFileWriter($dsn);

    $entry = new lmbLogEntry(LOG_ERR, 'foo');
    $writer->write($entry);

    $content = file_get_contents($writer->getLogFile());
    $this->assertPattern('/Error/', $content);
    $this->assertPattern('/foo/', $content);
  }
}
