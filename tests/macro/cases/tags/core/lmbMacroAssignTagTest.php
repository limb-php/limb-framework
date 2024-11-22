<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\tags\core;

use tests\macro\cases\lmbBaseMacroTestCase;

class lmbMacroAssignTagTest extends lmbBaseMacroTestCase
{
    function testAssignTag()
    {
        $template = '{{assign value="$#buffer" var="$output"/}}{$output}';
        $page = $this->_createMacroTemplate($template, 'tpl.html');

        $page->set('buffer', 'baz');

        $this->assertEquals('baz', $page->render());
    }
}
