<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\log;

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