<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\log\src;

use limb\log\src\exception\lmbClassNotFoundException;
use limb\net\src\lmbUri;

/**
 * class lmbLogWriterFactory.
 *
 * @package log
 * @version $Id$
 */
class lmbLogWriterFactory
{
    public static function createLogWriter($config)
    {
        if (is_string($config)) {
            $dsn = new lmbUri($config);
            $driver = $dsn->getScheme();
        }
        else {
            $driver = $config['driver'];
        }

        switch ($driver) {
            case 'null':
                return new lmbLogNullWriter();
            case 'echo':
                return new lmbLogEchoWriter();
            case 'file':
                return new lmbLogFileWriter($config);
            case 'plain_file':
                return new lmbLogPlainFileWriter($config);
            case 'firePHP':
                return new lmbLogFirePHPWriter($dsn);
            case 'phplog':
                return new lmbLogPHPLogWriter($config);
            case 'syslog':
                return new lmbLogSyslogWriter();
            case 'elastic':
                return new lmbLogElasticWriter($config);
            case 'redis':
                return new lmbLogRedisWriter($config, 'log');
            default:
                $writer_name = 'lmbLog' . ucfirst($driver) . 'Writer';
                $writerClassName = "limb\\log\\src\\" . $writer_name;

                throw new lmbClassNotFoundException($writerClassName, 'Log writer not found');
        }
    }
}
