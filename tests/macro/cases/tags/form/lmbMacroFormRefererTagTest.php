<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\tags\form;

use tests\macro\cases\lmbBaseMacroTestCase;

class lmbMacroFormRefererTagTest extends lmbBaseMacroTestCase
{
    protected $prev_ref;

    function setUp(): void
    {
        parent::setUp();

        $this->prev_ref = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "";
    }

    function tearDown(): void
    {
        parent::tearDown();

        $_SERVER["HTTP_REFERER"] = $this->prev_ref;
    }

    function testNoReferer()
    {
        $_SERVER["HTTP_REFERER"] = "";

        $template = '{{form name="my_form"}}{{form:referer}}{{/form}}';

        $page = $this->_createMacroTemplate($template, 'tpl.html');

        $out = $page->render();
        $this->assertEquals('<form name="my_form"></form>', $out);
    }

    function testReferer()
    {
        $_SERVER["HTTP_REFERER"] = "back.html";

        $template = '{{form name="my_form"}}{{form:referer}}{{/form}}';

        $page = $this->_createMacroTemplate($template, 'tpl.html');

        $out = $page->render();
        $this->assertEquals("<form name=\"my_form\"><input type='hidden' name='referer' value='back.html'></form>", $out);
    }
}
