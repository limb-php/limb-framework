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

    function getDefaultErrorConf(): string
    {
        return 'file://' . lmbEnv::get('LIMB_VAR_DIR') . 'log/error.log';
    }

    /** @deprecated */
    function getDefaultErrorDsn(): string
    {
        return $this->getDefaultErrorConf();
    }

    /** @TODO: improve */
    function getLogConfs(): array
    {
        $default_error_options = $this->getDefaultErrorConf();

        if (!$this->toolkit->hasConf('common'))
            return ['error' => $default_error_options];

        $conf = $this->toolkit->getConf('common');
        if (!isset($conf['logs']))
            return ['error' => $default_error_options];

        return $conf['logs'];
    }

    /** @TODO: improve */
    public function getLog($name = 'error'): LoggerInterface
    {
        if (isset($this->log[$name]) && $this->log[$name])
            return $this->log[$name];

        $this->log[$name] = new lmbLog();

        $logWriters = $this->getLogConfs();
        if(isset($logWriters[$name])) {
            if( is_array($logWriters[$name]) ) {
                foreach ($logWriters[$name] as $log_writer_key => $log_writer_options) {
                    if(is_numeric($log_writer_key) && $log_writer_options) {
                        $options = $log_writer_options;
                        $level = null;
                    } else {
                        $options = $log_writer_key;
                        $level = $log_writer_options;
                    }

                    $this->log[$name]->registerWriter(lmbLogWriterFactory::createLogWriter($options), $level);
                }
            } else {
                $options = $logWriters[$name];
                $this->log[$name]->registerWriter(lmbLogWriterFactory::createLogWriter($options));
            }
        }

        return $this->log[$name];
    }

    public function setLog($name, $log): void
    {
        $this->log[$name] = $log;
    }
}
