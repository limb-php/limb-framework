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
class lmbLogPHPLogWriter extends lmbLogFileWriter
{
    function getLogFile()
    {
        return ini_get('error_log');
    }
}
