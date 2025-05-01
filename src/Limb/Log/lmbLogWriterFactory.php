<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\Log;

use Limb\Log\Exception\lmbClassNotFoundException;
use Limb\Net\lmbUri;

/**
 * class lmbLogWriterFactory.
 *
 * @package log
 * @version $Id$
 */
class lmbLogWriterFactory
{
    public static function createLogWriter($config): lmbLogWriterInterface
    {
        if (is_string($config)) {
            $dsn = new lmbUri($config);
            $driver = $dsn->getScheme();
            $config = $dsn;
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
                return new lmbLogFirePHPWriter($config);
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
                $writerClassName = "Limb\\Log\\" . $writer_name;

                throw new lmbClassNotFoundException($writerClassName, 'Log writer not found');
        }
    }

}
