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

class lmbMacroWordDeclensionFilterTest extends lmbBaseMacroTestCase
{

    function _getUserResultForNumber($number)
    {
        $code = '{$#number|declension:"пользователь", "пользователей", "пользователя"}';
        $tpl = $this->_createMacroTemplate($code, 'word_declension_filter_tpl.html');
        $tpl->set('number', $number);
        $out = $tpl->render();
        return $out;
    }

    function testFunction()
    {
        $this->assertEquals("пользователь", $this->_getUserResultForNumber(1));
        $this->assertEquals("пользователь", $this->_getUserResultForNumber('1'));
        $this->assertEquals("пользователя", $this->_getUserResultForNumber(2));
        $this->assertEquals("пользователей", $this->_getUserResultForNumber(11));
        $this->assertEquals("пользователей", $this->_getUserResultForNumber(12));
        $this->assertEquals("пользователя", $this->_getUserResultForNumber(22));
        $this->assertEquals("пользователей", $this->_getUserResultForNumber(235));
        $this->assertEquals("пользователь", $this->_getUserResultForNumber(10001));
    }
}
