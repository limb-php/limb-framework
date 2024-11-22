<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\log\src;

/**
 * class lmbLogEchoWriter.
 *
 * @package log
 * @version $Id$
 */
class lmbLogSyslogWriter implements lmbLogWriterInterface
{
    const DELIMITER = '|||';

    function __construct()
    {
        openlog('LIMB', LOG_ODELAY | LOG_PID, LOG_USER);
    }

    function write(lmbLogEntry $entry)
    {
        $message = $entry->getLevelForHuman() . ': ' . str_replace("\n", self::DELIMITER, $entry->getMessage());
        syslog($entry->getLevel(), $message);
    }

    function __destruct()
    {
        closelog();
    }
}
