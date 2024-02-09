<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\i18n\cases\macro;

use Tests\macro\cases\lmbBaseMacroTestCase;
use limb\i18n\src\charset\lmbUTF8BaseDriver;
use limb\core\src\lmbSet;
use limb\i18n\src\charset\lmbI18nString;

require(dirname(__FILE__) . '/../.setup.php');

class lmbI18NClipMacroFilterTest extends lmbBaseMacroTestCase
{
    protected $prev_driver;

    function setUp(): void
    {
        parent::setUp();

        $this->prev_driver = lmbI18nString::useCharsetDriver(new lmbUTF8BaseDriver());
    }

    function tearDown(): void
    {
        lmbI18nString::useCharsetDriver($this->prev_driver);

        parent::tearDown();
    }

    function testLengthLimit()
    {
        $code = '{$#var|i18n_clip:3}';
        $tpl = $this->_createMacroTemplate($code, 'length_limit.html');
        $var = "что-то";
        $tpl->set('var', $var);
        $out = $tpl->render();
        $this->assertEquals('что', $out);
    }


    function testLengthLimitAsVariable()
    {
        $code = '{$#var|i18n_clip:$#limit}';
        $tpl = $this->_createMacroTemplate($code, 'length_limit.html');
        $var = "что-то";
        $tpl->set('var', $var);
        $tpl->set('limit', 3);
        $out = $tpl->render();
        $this->assertEquals('что', $out);
    }

    function testLengthLimitAndOffset()
    {
        $code = '{$#var|i18n_clip:3,5}';
        $tpl = $this->_createMacroTemplate($code, 'length_limit_and_offset.html');
        $var = "фреймворк для веб-приложений";
        $tpl->set('var', $var);
        $out = $tpl->render();
        $this->assertEquals('вор', $out);
    }

    function testWithSuffix()
    {
        $code = '{$#var|i18n_clip:3,5,"..."}';
        $tpl = $this->_createMacroTemplate($code, 'clip_with_suffix.html');
        $var = "фреймворк для веб-приложений";
        $tpl->set('var', $var);
        $out = $tpl->render();
        $this->assertEquals('вор...', $out);
    }

    function testSuffixNotUsedTooShortString()
    {
        $code = '{$#var|i18n_clip:10,"0","..."}';
        $tpl = $this->_createMacroTemplate($code, 'clip_suffix_not_used.html');
        $var = "фреймворк";
        $tpl->set('var', $var);
        $out = $tpl->render();
        $this->assertEquals('фреймворк', $out);
    }

    // don't know if boundary condition works for all cases. Should work for the simple ones.
    function testLongStringWordBoundary()
    {
        $code = '{$#var|i18n_clip:12,0,"...", "y"}';
        $tpl = $this->_createMacroTemplate($code, 'clip_with_word_bound.html');
        $var = "фреймворк для веб-приложений";
        $tpl->set('var', $var);
        $out = $tpl->render();
        $this->assertEquals('фреймворк для...', $out);
    }

    function testPathBasedDBELengthLimit()
    {
        $code = '{$#my.var|i18n_clip:3}';
        $tpl = $this->_createMacroTemplate($code, 'clip_path_based_dbe_with_limit.html');
        $data = new lmbSet(array('var' => 'что-то'));
        $tpl->set('my', $data);
        $out = $tpl->render();
        $this->assertEquals('что', $out);
    }

    function testQuoteRegexPatterns()
    {
        $code = '{$#var|i18n_clip:16,0,"...", "y"}';
        $tpl = $this->_createMacroTemplate($code, 'clip_with_regex_pattern.html');
        $var = "(фреймворк.*) для веб-приложений";
        $tpl->set('var', $var);
        $out = $tpl->render();
        $this->assertEquals('(фреймворк.*) для...', $out);
    }
}
