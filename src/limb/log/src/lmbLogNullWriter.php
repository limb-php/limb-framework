<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\log\src;

/**
 * class lmbLogNullWriter.
 *
 * @package log
 * @version $Id$
 */
class lmbLogNullWriter implements lmbLogWriterInterface
{
    function write(lmbLogEntry $entry)
    {
    }
}
