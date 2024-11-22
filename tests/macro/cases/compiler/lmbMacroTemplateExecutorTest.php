<?php
/*
 * Limb PHP Framework
 *
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
