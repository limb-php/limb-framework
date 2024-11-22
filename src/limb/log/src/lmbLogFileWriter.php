<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\log\src;

use limb\datetime\src\lmbDateTime;
use limb\fs\src\lmbFs;
use limb\fs\src\exception\lmbFsException;
use limb\net\src\lmbUri;

/**
 * class lmbLogFileWriter.
 *
 * @package log
 * @version $Id$
 */
class lmbLogFileWriter implements lmbLogWriterInterface
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
        $this->_appendToFile($this->getLogFile(), $entry->asText(), $entry->getTime());
    }

    protected function _appendToFile($file_name, $message, $stamp)
    {
        lmbFs::mkdir(dirname($file_name), 0775);
        $file_existed = file_exists($file_name);

        if ($fh = fopen($file_name, 'a')) {
            @flock($fh, LOCK_EX);
            $time = (new lmbDateTime($stamp))->format("Y-m-d h:i:s");

            $log_message = "=========================[{$time}]";

            if (isset($_SERVER['REMOTE_ADDR']))
                $log_message .= '[' . $_SERVER['REMOTE_ADDR'] . ']';

            if (isset($_SERVER['REQUEST_URI']))
                $log_message .= '[' . $_SERVER['REQUEST_METHOD'] . ': ' . $_SERVER['REQUEST_URI'] . ']';

            if (isset($_SERVER['HTTP_REFERER']))
                $log_message .= '[REF: ' . $_SERVER['HTTP_REFERER'] . ']';

            $log_message .= "=========================\n" . $message;

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
