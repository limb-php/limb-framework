<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\log\src;

use limb\datetime\src\lmbDateTime;

/**
 * class lmbLogRedisWriter.
 *
 * @package log
 * @version $Id$
 */
class lmbLogRedisWriter implements lmbLogWriterInterface
{
    private $redisClient;
    private string $redisKey;

    function __construct($dsn, string $key)
    {
        $this->redisClient = new \Predis\Client($dsn);
        $this->redisKey = $key;
    }

    function write(lmbLogEntry $entry)
    {
        $formated = $this->formatEntry($entry);

        $this->redisClient->rpush($this->redisKey, $formated);
    }

    protected function formatEntry(lmbLogEntry $entry): mixed
    {
        $time = (new lmbDateTime($entry->getTime()))->format("Y-m-d h:i:s");

        $log_message = $time . ": ";
        if (isset($_SERVER['REMOTE_ADDR']))
            $log_message .= '[' . $_SERVER['REMOTE_ADDR'] . ']';
        if (isset($_SERVER['REQUEST_URI']))
            $log_message .= '[' . $_SERVER['REQUEST_METHOD'] . ': ' . $_SERVER['REQUEST_URI'] . ']';
        if (isset($_SERVER['HTTP_REFERER']))
            $log_message .= '[REF: ' . $_SERVER['HTTP_REFERER'] . ']';
        $log_message .= $entry->asText();

        return $log_message;
    }
}
