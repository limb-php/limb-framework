<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

namespace Tests\core\cases\src;

use limb\core\src\lmbProxy;

class ProxyTestingStub extends lmbProxy
{
    protected $extra_attrib = 'whatever';
    protected $original_mock;
    public $create_calls = 0;

    function __construct($mock)
    {
        $this->original_mock = $mock;
    }

    protected function _createOriginalObject()
    {
        $this->create_calls++;
        return $this->original_mock;
    }
}
