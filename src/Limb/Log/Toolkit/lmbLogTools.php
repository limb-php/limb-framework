<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\Log\Toolkit;

use Limb\Config\Toolkit\lmbConfTools;
use Limb\Toolkit\lmbAbstractTools;
use Limb\Core\lmbEnv;
use Limb\Log\lmbLog;
use Limb\Log\lmbLogWriterFactory;
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
                foreach ($logWriters[$name] as $log_writer_key => $log_writer_value) {
                    if(!$log_writer_key) {
                        $dsn = $log_writer_value;
                        $level = null;
                    } else {
                        $dsn = $log_writer_key;
                        $level = $log_writer_value;
                    }

                    $this->log[$name]->registerWriter(lmbLogWriterFactory::createLogWriter($dsn), $level);
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
