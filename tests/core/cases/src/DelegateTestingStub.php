<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\core\cases\src;

class DelegateTestingStub
{
    public $instance_arg;
    public $instance_called = false;

    public $instance_arg1;
    public $instance_arg2;

    static public $static_arg;
    static public $static_called = false;

    function instanceMethod($arg)
    {
        $this->instance_arg = $arg;
        $this->instance_called = true;
    }

    function instanceReturningMethod($arg)
    {
        $this->instance_called = true;
        return $arg;
    }

    function instanceMethodWithManyArgs($arg1, $arg2)
    {
        $this->instance_arg1 = $arg1;
        $this->instance_arg2 = $arg2;
    }

    static function staticMethod($arg)
    {
        self::$static_arg = $arg;
        self::$static_called = true;
    }
}
