<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\log\src;

interface lmbLogWriterInterface
{
    function write(lmbLogEntry $entry);
}
