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

/**
 * class lmbLogTools.
 *
 * @package log
 * @version $Id: lmbWebAppTools.php 8011 2009-12-25 08:51:27Z
 */
class lmbLogTools extends lmbAbstractTools
{
  protected $log;

  static function getRequiredTools()
  {
    return [
      lmbConfTools::class
    ];
  }

  function getLogDSNes()
  {
    $default_dsn = 'file://'.lmbEnv::get('LIMB_VAR_DIR').'log/error.log';

    if(!$this->toolkit->hasConf('common'))
      return array($default_dsn);

    $conf = $this->toolkit->getConf('common');
    if(!isset($conf['logs']))
      return array($default_dsn);

    return $conf['logs'];
  }

  public function getLog()
  {
    if($this->log)
      return $this->log;

    $this->log = new lmbLog();
    foreach($this->getLogDSNes() as $dsn)
      $this->log->registerWriter(lmbLogWriterFactory::createLogWriter($dsn));

    return $this->log;
  }

  public function setLog($log)
  {
      $this->log = $log;
  }
}
