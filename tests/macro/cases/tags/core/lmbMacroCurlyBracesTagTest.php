<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\tags\core;

use tests\macro\cases\lmbBaseMacroTestCase;

class lmbMacroCurlyBracesTagTest extends lmbBaseMacroTestCase
{
    function testBraces()
    {
        $template = "{{cbo}}{{cbo}}macro{{cbc}}{{cbc}}";

        $page = $this->_createMacroTemplate($template, 'tpl.html');

        $this->assertEquals("{{macro}}", $page->render());
    }
}
