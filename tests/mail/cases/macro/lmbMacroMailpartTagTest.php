<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\mail\cases\macro;

use tests\macro\cases\lmbBaseMacroTestCase;

require_once dirname(__FILE__) . '/.setup.php';

class lmbMacroMailpartTagTest extends lmbBaseMacroTestCase
{
    function testSinglePart()
    {
        $list = '{{mailpart name="test"}}ZZZ{{/mailpart}}';

        $list_tpl = $this->_createTemplate($list, 'mailpart.html');
        $macro = $this->_createMacro($list_tpl);

        $out = $macro->render();
        $this->assertEquals('<mailpart name="test"><![CDATA[ZZZ]]></mailpart>', $out);
    }

    function testManyParts()
    {
        $list = '{{mailpart name="name_a"}}ZZZ{{/mailpart}}_between_{{mailpart name="name_b"}}YYY{{/mailpart}}';

        $list_tpl = $this->_createTemplate($list, 'mailpart.html');
        $macro = $this->_createMacro($list_tpl);

        $out = $macro->render();
        $this->assertEquals('<mailpart name="name_a"><![CDATA[ZZZ]]></mailpart>_between_<mailpart name="name_b"><![CDATA[YYY]]></mailpart>', $out);
    }
}
