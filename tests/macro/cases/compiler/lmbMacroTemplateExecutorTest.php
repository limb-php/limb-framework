<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\compiler;

use tests\macro\cases\lmbBaseMacroTestCase;
use limb\macro\src\compiler\lmbMacroTemplateExecutor;

class lmbMacroTemplateExecutorTest extends lmbBaseMacroTestCase
{
    function testPassVars()
    {
        $tpl = new lmbMacroTemplateExecutor($this->_createMacroConfig(), array('foo' => 'foo', 'bar' => 'bar'));
        $tpl->set('zoo', 'zoo');
        $this->assertEquals('foo', $tpl->foo);
        $this->assertEquals('bar', $tpl->bar);
        $this->assertEquals('zoo', $tpl->zoo);
    }

    function testMissingVarIsEmpty()
    {
        $tpl = new lmbMacroTemplateExecutor($this->_createMacroConfig());
        $this->assertEquals('', $tpl->junk);
    }
}
