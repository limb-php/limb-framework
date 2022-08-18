<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
use limb\tests_runner\src\lmbTestShellUI;
use limb\cli\src\lmbCliBaseCmd;

/**
 * class UnitCliCmd.
 *
 * @package tests_runner
 * @version $Id$
 */
class UnitCliCmd extends lmbCliBaseCmd
{
  function execute($argv)
  {
    set_time_limit(0);
    error_reporting(E_ALL);

    $ui = new lmbTestShellUI($argv);
    $ui->setPosixMode(false);
    return ($ui->runEmbedded() ? 0 : 1);
  }

  function help($argv)
  {
    $ui = new lmbTestShellUI($argv);
    echo $ui->help('unit');
  }
}


