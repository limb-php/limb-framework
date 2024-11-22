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
class lmbLogEchoWriter implements lmbLogWriterInterface
{
    function write(lmbLogEntry $entry)
    {
        echo $entry->toString();
    }
}
