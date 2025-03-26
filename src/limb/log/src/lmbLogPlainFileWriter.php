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
 * class lmbLogPlainFileWriter.
 *
 * @package log
 * @version $Id$
 */
class lmbLogPlainFileWriter implements lmbLogWriterInterface
{
    protected $log_file;
    protected $options = [
        'dateFormat' => 'U',
        'filenameFormat' => '{filename}' //'{filename}-{date}'
    ];

    function __construct($dsn_or_path, $options = [])
    {
        if( is_a($dsn_or_path, lmbUri::class) )
            $this->log_file = $dsn_or_path->getPath();
        else
            $this->log_file = $dsn_or_path;

        if(isset($options['dateFormat']))
            $this->options['dateFormat'] = $options['dateFormat'];
        if(isset($options['filenameFormat']))
            $this->options['filenameFormat'] = $options['filenameFormat'];
    }

    function write(lmbLogEntry $entry)
    {
        $this->_appendToFile($this->getLogFile($entry->getTime()), $entry);
    }

    protected function _appendToFile($file_name, $entry)
    {
        lmbFs::mkdir(dirname($file_name), 0775);
        $file_existed = file_exists($file_name);

        if ($fh = fopen($file_name, 'a')) {
            @flock($fh, LOCK_EX);

            $formated = $this->formatEntry($entry);

            fwrite($fh, $formated . PHP_EOL);
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

    protected function formatEntry(lmbLogEntry $entry): mixed
    {
        $time = (new lmbDateTime($entry->getTime()))->format("Y-m-d h:i:s");

        $log_message = "[{$time}]" . " " . $entry->getMessage();

        return $log_message;
    }

    function getLogFile($stamp)
    {
        $fileInfo = pathinfo($this->log_file);
        $timedFilename = str_replace(
            ['{filename}', '{date}'],
            [$fileInfo['filename'], date($this->options['dateFormat'], $stamp)],
            ($fileInfo['dirname'] ?? '') . '/' . $this->options['filenameFormat']
        );

        if (isset($fileInfo['extension'])) {
            $timedFilename .= '.'.$fileInfo['extension'];
        }

        return $timedFilename;
    }
}
