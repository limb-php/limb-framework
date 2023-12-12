<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\log\src;

use limb\net\src\lmbUri;

/**
 * class lmbLogEchoWriter.
 *
 * @package log
 * @version $Id$
 */
class lmbLogEchoWriter implements lmbLogWriterInterface
{
    function __construct(lmbUri $dsn)
    {
    }

    function write(lmbLogEntry $entry)
    {
        echo $entry->toString();
    }
}
