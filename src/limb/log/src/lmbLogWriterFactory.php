<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\log\src;

use limb\fs\src\exception\lmbFileNotFoundException;
use limb\net\src\lmbUri;

/**
 * class lmbLogWriterFactory.
 *
 * @package log
 * @version $Id$
 */
class lmbLogWriterFactory
{
    public static function createLogWriter($dsn)
    {
        if (!is_object($dsn))
            $dsn = new lmbUri($dsn);

        $writer_name = 'lmbLog' . ucfirst($dsn->getScheme()) . 'Writer';
        $writerClassName = "limb\\log\\src\\" . $writer_name;

        try {
            $writer = new $writerClassName($dsn);
        } catch (\Error $e) {
            throw new lmbFileNotFoundException($writerClassName, 'Log writer not found');
        }

        return $writer;
    }
}
