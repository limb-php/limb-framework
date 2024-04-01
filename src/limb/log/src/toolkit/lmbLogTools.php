<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\log\src\toolkit;

use limb\config\src\toolkit\lmbConfTools;
use limb\toolkit\src\lmbAbstractTools;
use limb\core\src\lmbEnv;
use limb\log\src\lmbLog;
use limb\log\src\lmbLogWriterFactory;
use Psr\Log\LoggerInterface;

/**
 * class lmbLogTools.
 *
 * @package log
 * @version $Id: lmbWebAppTools.php 8011 2009-12-25 08:51:27Z
 */
class lmbLogTools extends lmbAbstractTools
{
    /** @var $log LoggerInterface */
    protected $log;

    /** @return class-string[] */
    static function getRequiredTools()
    {
        return [
            lmbConfTools::class
        ];
    }

    function getDefaultErrorDsn(): string
    {
        return 'file://' . lmbEnv::get('LIMB_VAR_DIR') . 'log/error.log';
    }

    /** @TODO: improve */
    function getConfLogDSNes(): array
    {
        $default_error_dsn = $this->getDefaultErrorDsn();

        if (!$this->toolkit->hasConf('common'))
            return ['error' => $default_error_dsn];

        $conf = $this->toolkit->getConf('common');
        if (!isset($conf['logs']))
            return ['error' => $default_error_dsn];

        return $conf['logs'];
    }

    /** @TODO: improve */
    public function getLog($name = 'error'): LoggerInterface
    {
        if (isset($this->log[$name]) && $this->log[$name])
            return $this->log[$name];

        $this->log[$name] = new lmbLog();

        $logWriters = $this->getConfLogDSNes();
        if(isset($logWriters[$name])) {
            if( is_array($logWriters[$name]) ) {
                foreach ($logWriters[$name] as $dsn) {
                    $this->log[$name]->registerWriter(lmbLogWriterFactory::createLogWriter($dsn));
                }
            } else {
                $dsn = $logWriters[$name];
                $this->log[$name]->registerWriter(lmbLogWriterFactory::createLogWriter($dsn));
            }
        }

        return $this->log[$name];
    }

    /** @TODO: improve */
    public function setLog($name, $log): void
    {
        $this->log[$name] = $log;
    }
}
