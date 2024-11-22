<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\tags\core;

use tests\macro\cases\lmbBaseMacroTestCase;

class lmbMacroCopyAndCutTagsTest extends lmbBaseMacroTestCase
{
    function testCopyTag()
    {
        $template = '{{copy into="$#my_buffer"}}F|{{/copy}}N|{$#my_buffer}';

        $page = $this->_createMacroTemplate($template, 'tpl.html');

        $this->assertEquals('F|N|F|', $page->render());
    }

    function testCutTag()
    {
        $template = '{{cut into="$#my_buffer"}}F|{{/cut}}N|{$#my_buffer}';

        $page = $this->_createMacroTemplate($template, 'tpl.html');

        $this->assertEquals('N|F|', $page->render());
    }
}
