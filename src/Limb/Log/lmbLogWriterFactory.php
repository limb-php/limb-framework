<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\log;

use limb\log\exception\lmbClassNotFoundException;
use limb\net\lmbUri;

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

        switch ($dsn->getScheme()) {
            case 'null':
                return new lmbLogNullWriter();
            case 'echo':
                return new lmbLogEchoWriter();
            case 'file':
                return new lmbLogFileWriter($dsn);
            case 'plain_file':
                return new lmbLogPlainFileWriter($dsn);
            case 'firePHP':
                return new lmbLogFirePHPWriter($dsn);
            case 'phplog':
                return new lmbLogPHPLogWriter($dsn->toString());
            case 'syslog':
                return new lmbLogSyslogWriter();
            case 'redis':
                return new lmbLogRedisWriter($dsn->toString(), 'log');
            default:
                $writer_name = 'lmbLog' . ucfirst($dsn->getScheme()) . 'Writer';
                $writerClassName = "limb\\log\\src\\" . $writer_name;

                throw new lmbClassNotFoundException($writerClassName, 'Log writer not found');
        }
    }
}
