<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\Log;

use Limb\Datetime\lmbDateTime;
use Limb\Fs\lmbFs;
use Limb\Fs\Exception\lmbFsException;
use Limb\Net\lmbUri;

/**
 * class lmbLogPlainFileWriter.
 *
 * @package log
 * @version $Id$
 */
class lmbLogPlainFileWriter implements lmbLogWriterInterface
{
    protected $log_file;

    function __construct($dsn_or_path)
    {
        if( is_a($dsn_or_path, lmbUri::class) )
            $this->log_file = $dsn_or_path->getPath();
        else
            $this->log_file = $dsn_or_path;
    }

    function write(lmbLogEntry $entry)
    {
        $this->_appendToFile($this->getLogFile(), $entry->getMessage(), $entry->getTime());
    }

    protected function _appendToFile($file_name, $message, $stamp)
    {
        lmbFs::mkdir(dirname($file_name), 0775);
        $file_existed = file_exists($file_name);

        if ($fh = fopen($file_name, 'a')) {
            @flock($fh, LOCK_EX);
            $time = (new lmbDateTime($stamp))->format("Y-m-d h:i:s");

            $log_message = "[{$time}]" . " " . $message;

            fwrite($fh, $log_message);
            @flock($fh, LOCK_UN);
            fclose($fh);
            if (!$file_existed)
                chmod($file_name, 0664);
        } else {
            throw new lmbFsException("Cannot open log file '$file_name' for writing\n" .
                "The web server must be allowed to modify the file.\n" .
                "File logging for '$file_name' is disabled.");
        }
    }

    function getLogFile()
    {
        return $this->log_file;
    }
}
