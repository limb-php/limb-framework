<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cli;

use limb\core\lmbString;
use limb\core\exception\lmbException;
use limb\core\lmbEnv;

lmbEnv::setor('LIMB_CLI_INCLUDE_PATH', 'cli;limb/*/cli');

/**
 * class lmbCliRunner.
 *
 * @package cli
 * @version $Id$
 */
class lmbCliRunner
{
    protected $input;
    protected $output;
    protected $return_on_exit = false;
    protected $use_exception = false;
    protected $search_path;

    function __construct($input, $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->search_path = lmbEnv::get('LIMB_CLI_INCLUDE_PATH');
    }

    static function commandToClass($name)
    {
        return lmbString::camel_case(self::sanitizeName($name)) . 'CliCmd';
    }

    static function actionToMethod($name)
    {
        return lmbString::camel_case(self::sanitizeName($name));
    }

    static function sanitizeName($name)
    {
        $name = preg_replace('~\W~', '_', $name);
        return $name;
    }

    function setCommandSearchPath($path)
    {
        $this->search_path = $path;
    }

    function returnOnExit($flag = true)
    {
        $this->return_on_exit = $flag;
    }

    function throwOnError($flag = true)
    {
        $this->use_exception = $flag;
    }

    function execute()
    {
        if (!$command_name = $this->input->getArgument(0))
            $this->_error('You should specify command');

        if (!$command = $this->_mapCommandToObject($command_name))
            $this->_error("Command '$command_name' is invalid(could not map it to the command class)");

        $argv = $this->input->getArgv();
        array_shift($argv);

        $action = 'execute';

        if ($arg = $this->input->getArgument(1)) {
            $method = self::actionToMethod($arg);
            if (method_exists($command, $method)) {
                $action = $method;
                array_shift($argv);
            }
        }

        if (!method_exists($command, $action))
            throw new lmbException('Undefined method ' . $action);

        $result = (int)$command->$action($argv);
        return $this->_exit($result);
    }

    protected function _exit($code = 0)
    {
        if ($this->return_on_exit)
            return $code;
        else
            exit($code);
    }

    protected function _error($message = '')
    {
        if ($this->use_exception)
            throw new lmbException($message);
        else
            exit(1);
    }

    protected function _mapCommandToObject($command_name)
    {
        $items = explode(';', $this->search_path);
        foreach ($items as $item) {
            $class = self::commandToClass($command_name);

            return new $class($this->output);
        }

        return false;
    }
}
