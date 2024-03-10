<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

namespace tests\core\cases\src;

class DelegatesChainTestingStubObject
{

    public $invoked = 0;
    public $last_arg = null;
    public $last_arg2 = null;

    protected $return;

    function __construct($return = 'invoked')
    {
        $this->return = $return;
    }

    function invokable($arg = null, $arg2 = null)
    {
        $this->invoked++;
        $this->last_arg = $arg;
        $this->last_arg2 = $arg2;
        return $this->return;
    }

}
