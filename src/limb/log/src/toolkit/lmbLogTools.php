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

    function getLogDSNes(): array
    {
        $default_error_dsn = $this->getDefaultErrorDsn();

        if (!$this->toolkit->hasConf('common'))
            return ['error' => $default_error_dsn];

        $conf = $this->toolkit->getConf('common');
        if (!isset($conf['logs']))
            return ['error' => $default_error_dsn];

        return $conf['logs'];
    }

    public function getLog(): LoggerInterface
    {
        if ($this->log)
            return $this->log;

        $this->log = new lmbLog();
        foreach ($this->getLogDSNes() as $name => $dsn)
            $this->log->registerWriter($name, lmbLogWriterFactory::createLogWriter($dsn));

        return $this->log;
    }

    public function setLog($log): void
    {
        $this->log = $log;
    }
}
