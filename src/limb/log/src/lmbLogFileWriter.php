<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\log\src;

use limb\datetime\src\lmbDateTime;
use limb\fs\src\lmbFs;
use limb\log\src\exception\lmbLogWriterException;
use limb\net\src\lmbIp;
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
        $this->_appendToFile($this->getLogFile(), $entry);
    }

    protected function _appendToFile($file_name, $entry)
    {
        lmbFs::mkdir(dirname($file_name), 0775);

        $errorMsg = null;

        try {
            $fh = fopen($file_name, 'a');
            @chmod($file_name, 0664);

            @flock($fh, LOCK_EX);

            $formated = $this->formatEntry($entry);

            fwrite($fh, $formated . PHP_EOL);
            @flock($fh, LOCK_UN);
            fclose($fh);
        }
        catch (\Throwable $e) {
            $errorMsg = $e->getMessage();
        }

        if($errorMsg) {
            //throw new lmbLogWriterException($errorMsg);
        }
    }

    protected function formatEntry(lmbLogEntry $entry): mixed
    {
        $time = (new lmbDateTime($entry->getTime()))->format("Y-m-d h:i:s");

        $log_message = "=========================[{$time}]";

        $log_message .= '[' . lmbIp::getRealIp() . ']';

        if (isset($_SERVER['REQUEST_URI']))
            $log_message .= '[' . $_SERVER['REQUEST_METHOD'] . ': ' . $_SERVER['REQUEST_URI'] . ']';

        if (isset($_SERVER['HTTP_REFERER']))
            $log_message .= '[REF: ' . $_SERVER['HTTP_REFERER'] . ']';

        $log_message .= "=========================\n" . $entry->asText();

        return $log_message;
    }

    function getLogFile()
    {
        return $this->log_file;
    }
}
