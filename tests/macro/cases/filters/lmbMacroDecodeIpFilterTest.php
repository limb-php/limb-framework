<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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
