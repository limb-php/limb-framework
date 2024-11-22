<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\filters;

use tests\macro\cases\lmbBaseMacroTestCase;

class lmbMacroDecodeIpFilterTest extends lmbBaseMacroTestCase
{
    function testSimple()
    {
        $code = '{$#encoded_ip|decode_ip}';
        $tpl = $this->_createMacroTemplate($code, 'tpl.html');
        $tpl->set('encoded_ip', '-1062666387');
        $out = $tpl->render();
        $this->assertEquals('192.168.255.109', $out);
    }
}
