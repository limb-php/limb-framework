<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\filters;

use tests\macro\cases\lmbBaseMacroTestCase;

class lmbMacroRecognizeUrlsFilterTest extends lmbBaseMacroTestCase
{
    function testSimple()
    {
        $code = '{$#str|recognize_urls}';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $tpl->set('str', 'foo http://aaa.com bar');
        $out = $tpl->render();
        $this->assertEquals('foo <a href="http://aaa.com">http://aaa.com</a> bar', $out);
    }

    function testUrlWithWithoutHttpAndWithWWW()
    {
        $code = '{$#str|recognize_urls}';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $tpl->set('str', 'foo www.aaa.com bar');
        $out = $tpl->render();
        $this->assertEquals('foo <a href="http://www.aaa.com">www.aaa.com</a> bar', $out);
    }
}
