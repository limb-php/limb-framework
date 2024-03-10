<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\tags\pager;

use tests\macro\cases\lmbBaseMacroTestCase;

class lmbMacroPagerTagTest extends lmbBaseMacroTestCase
{
    protected $old_get;
    protected $old_server;

    function setUp(): void
    {
        parent::setUp();

        $this->old_get = $_GET;
        $this->old_server = $_SERVER;

        $_SERVER['REQUEST_URI'] = 'test.com';
        $_GET = array();
    }

    function tearDown(): void
    {
        $_GET = $this->old_get;
        $_SERVER = $this->old_server;

        parent::tearDown();
    }

    function testPager()
    {
        $template = '{{pager id="test_pager" items="10" total_items="$#items_count"}}' .
            '{{pager:first}}F-{$href}|{{/pager:first}}' .
            '{{pager:prev}}P-{$href}|{{/pager:prev}}' .
            '{{pager:list}}' .
            '{{pager:current}}C-{$href}|{$number}|{{/pager:current}}' .
            '{{pager:number}}N-{$href}|{$number}|{{/pager:number}}' .
            '{{pager:separator}}**{{/pager:separator}}' .
            '{{/pager:list}}' .
            '{{pager:next}}X-{$href}|{{/pager:next}}' .
            '{{pager:last}}L-{$href}|{{/pager:last}}' .
            '{{/pager}}';

        $page = $this->_createMacroTemplate($template, 'tpl.html');
        $page->set('items_count', 40);

        $_GET['test_pager'] = 2;

        $expected = 'F-test.com|' .
            'P-test.com|' .
            'N-test.com|1|**' .
            'C-test.com?test_pager=2|2|**' .
            'N-test.com?test_pager=3|3|**N-test.com?test_pager=4|4|' .
            'X-test.com?test_pager=3|' .
            'L-test.com?test_pager=4|';

        $this->assertEquals($expected, $page->render());
    }

    function testPagerProperties()
    {
        $template = '{{pager id="test_pager" items="5" total_items="$#items_count"}}' .
            '{$total_items}|{$total_pages}|{$items_per_page}|' .
            'from:{$begin_item_number}|to:{$end_item_number}' .
            '{{/pager}}';

        $page = $this->_createMacroTemplate($template, 'tpl.html');
        $page->set('items_count', 40);

        $_GET['test_pager'] = 2;

        $this->assertEquals('40|8|5|from:6|to:10', $page->render());
    }

    function testSinglePageOnlyNotRenderAnything()
    {
        $template = '{{pager id="test_pager" items="10" total_items="$#items_count"}}' .
            '{{pager:first}}F-{$href}|{{/pager:first}}' .
            '{{pager:prev}}P-{$href}|{{/pager:prev}}' .
            '{{pager:list}}' .
            '{{pager:current}}C-{$href}|{$number}|{{/pager:current}}' .
            '{{pager:number}}N-{$href}|{$number}|{{/pager:number}}' .
            '{{/pager:list}}' .
            '{{pager:next}}X-{$href}|{{/pager:next}}' .
            '{{pager:last}}L-{$href}|{{/pager:last}}' .
            '{{/pager}}';

        $page = $this->_createMacroTemplate($template, 'tpl.html');
        $page->set('items_count', 5);

        $this->assertEquals('', $page->render());
    }

    function testFistPage()
    {
        $template = '{{pager id="test_pager" items="10" total_items="$#items_count"}}' .
            '{{pager:first}}F-{$href}|{{/pager:first}}' .
            '{{pager:prev}}P-{$href}|{{/pager:prev}}' .
            '{{pager:list}}' .
            '{{pager:current}}C-{$href}|{$number}|{{/pager:current}}' .
            '{{pager:number}}N-{$href}|{$number}|{{/pager:number}}' .
            '{{/pager:list}}' .
            '{{/pager}}';

        $page = $this->_createMacroTemplate($template, 'tpl.html');
        $page->set('items_count', 40);

        $expected = 'C-test.com|1|' .
            'N-test.com?test_pager=2|2|N-test.com?test_pager=3|3|N-test.com?test_pager=4|4|';

        $this->assertEquals($expected, $page->render());
    }

    function testLastPage()
    {
        $template = '{{pager id="test_pager" items="10" total_items="$#items_count"}}' .
            '{{pager:first}}F-{$href}|{{/pager:first}}' .
            '{{pager:prev}}P-{$href}|{{/pager:prev}}' .
            '{{pager:list}}' .
            '{{pager:current}}C-{$href}|{$number}|{{/pager:current}}' .
            '{{pager:number}}N-{$href}|{$number}|{{/pager:number}}' .
            '{{/pager:list}}' .
            '{{/pager}}';

        $page = $this->_createMacroTemplate($template, 'tpl.html');
        $page->set('items_count', 30);

        $_GET['test_pager'] = 3;

        $expected = 'F-test.com|' .
            'P-test.com?test_pager=2|' .
            'N-test.com|1|' .
            'N-test.com?test_pager=2|2|' .
            'C-test.com?test_pager=3|3|';

        $this->assertEquals($expected, $page->render());
    }

    function testElipsesBothSides()
    {
        $template = '{{pager id="test_pager" items="5" total_items="$#items_count" pages_in_middle="3" pages_in_sides="3"}}' .
            '{{pager:list}}' .
            '{{pager:current}}C-{$number}|{{/pager:current}}' .
            '{{pager:number}}N-{$number}|{{/pager:number}}' .
            '{{pager:elipses}}...{{/pager:elipses}}' .
            '{{/pager:list}}' .
            '{{/pager}}';

        $page = $this->_createMacroTemplate($template, 'tpl.html');
        $page->set('items_count', 65);

        $_GET['test_pager'] = 7;

        $this->assertEquals('N-1|N-2|N-3|...N-6|C-7|N-8|...N-11|N-12|N-13|', $page->render());
    }

    function testElipsesBothSidesAndSeparator()
    {
        $template = '{{pager id="test_pager" items="5" pages_in_middle="3" total_items="$#items_count" pages_in_sides="3"}}' .
            '{{pager:list}}' .
            '{{pager:current}}C-{$number}{{/pager:current}}' .
            '{{pager:number}}N-{$number}{{/pager:number}}' .
            '{{pager:elipses}}...{{/pager:elipses}}' .
            '{{pager:separator}}|{{/pager:separator}}' .
            '{{/pager:list}}' .
            '{{/pager}}';

        $page = $this->_createMacroTemplate($template, 'tpl.html');
        $page->set('items_count', 65);

        $_GET['test_pager'] = 7;

        $this->assertEquals('N-1|N-2|N-3...N-6|C-7|N-8...N-11|N-12|N-13', $page->render());
    }

    function testElipsesBothSidesNoPagesInSides()
    {
        $template = '{{pager id="test_pager" items="5" pages_in_middle="3" total_items="$#items_count" pages_in_sides="0"}}' .
            '{{pager:list}}' .
            '{{pager:current}}C-{$number}|{{/pager:current}}' .
            '{{pager:number}}N-{$number}|{{/pager:number}}' .
            '{{pager:elipses}}...{{/pager:elipses}}' .
            '{{/pager:list}}' .
            '{{/pager}}';

        $page = $this->_createMacroTemplate($template, 'tpl.html');
        $page->set('items_count', 65);

        $_GET['test_pager'] = 7;

        $this->assertEquals('...N-6|C-7|N-8|...', $page->render());
    }

    function testElipsesLeftOnly()
    {
        $template = '{{pager id="test_pager" items="5" pages_in_middle="3" total_items="$#items_count" pages_in_sides="3"}}' .
            '{{pager:list}}' .
            '{{pager:current}}C-{$number}|{{/pager:current}}' .
            '{{pager:number}}N-{$number}|{{/pager:number}}' .
            '{{pager:elipses}}...{{/pager:elipses}}' .
            '{{/pager:list}}' .
            '{{/pager}}';

        $page = $this->_createMacroTemplate($template, 'tpl.html');
        $page->set('items_count', 55);

        $_GET['test_pager'] = 7;

        $this->assertEquals('N-1|N-2|N-3|...N-6|C-7|N-8|N-9|N-10|N-11|', $page->render());
    }

    function testNoElipsesForSingleItemGap()
    {
        $template = '{{pager id="test_pager" items="5" pages_in_middle="3" total_items="$#items_count" pages_in_sides="3"}}' .
            '{{pager:list}}' .
            '{{pager:current}}C-{$number}|{{/pager:current}}' .
            '{{pager:number}}N-{$number}|{{/pager:number}}' .
            '{{pager:elipses}}...{{/pager:elipses}}' .
            '{{/pager:list}}' .
            '{{/pager}}';

        $page = $this->_createMacroTemplate($template, 'tpl.html');
        $page->set('items_count', 35);

        $_GET['test_pager'] = 5;

        $this->assertEquals('N-1|N-2|N-3|N-4|C-5|N-6|N-7|', $page->render());
    }

    function testElipsesMiddleCountAlwaysDisplayed()
    {
        $template = '{{pager id="test_pager" items="2" pages_in_middle="9" total_items="$#items_count" pages_in_sides="0"}}' .
            '{{pager:list}}' .
            '{{pager:current}}C-{$number}|{{/pager:current}}' .
            '{{pager:number}}N-{$number}|{{/pager:number}}' .
            '{{pager:elipses}}...{{/pager:elipses}}' .
            '{{/pager:list}}' .
            '{{/pager}}';

        //always 8 items in middle
        $page = $this->_createMacroTemplate($template, 'tpl.html');
        $page->set('items_count', 35);

        $_GET['test_pager'] = 2;


        $expected = 'N-1|C-2|N-3|N-4|N-5|N-6|N-7|N-8|N-9|...';
        $this->assertEquals($page->render(), $expected);

        //always 9 items in middle
        $page = $this->_createMacroTemplate($template, 'tpl2.html');
        $page->set('items_count', 35);

        $_GET['test_pager'] = 6;


        $expected = '...N-2|N-3|N-4|N-5|C-6|N-7|N-8|N-9|N-10|...';
        $this->assertEquals($page->render(), $expected);

        //if pages count less than pages_in_middle
        $page = $this->_createMacroTemplate($template, 'tpl3.html');
        $page->set('items_count', 5);

        $_GET['test_pager'] = 2;

        $this->assertEquals('N-1|C-2|N-3|', $page->render());
    }

    function testSections()
    {
        $template = '{{pager id="test_pager" items="10" pages_per_section="2" total_items="$#items_count"}}' .
            '{{pager:list}}' .
            '{{pager:current}}C-{$href}|{$number}|{{/pager:current}}' .
            '{{pager:number}}N-{$href}|{$number}|{{/pager:number}}' .
            '{{pager:section}}S-{$href}|{$section_begin_page}|{$section_end_page}|{{/pager:section}}' .
            '{{/pager:list}}' .
            '{{/pager}}';

        $page = $this->_createMacroTemplate($template, 'tpl.html');
        $page->set('items_count', 60);

        $_GET['test_pager'] = 3;

        $expected = 'S-test.com?test_pager=2|1|2|' .
            'C-test.com?test_pager=3|3|' .
            'N-test.com?test_pager=4|4|' .
            'S-test.com?test_pager=5|5|6|';

        $this->assertEquals($expected, $page->render());
    }

    function testDisabledTagsShown()
    {
        $template = '{{pager id="test_pager" items="10" total_items="$#items_count"}}' .
            '{{pager:first:disabled}}F-|{{/pager:first:disabled}}' .
            '{{pager:prev:disabled}}P-|{{/pager:prev:disabled}}' .
            '{{pager:next:disabled}}|-X{{/pager:next:disabled}}' .
            '{{pager:last:disabled}}|-L{{/pager:last:disabled}}' .
            '{{/pager}}';

        $page = $this->_createMacroTemplate($template, 'tpl.html');
        $page->set('items_count', 1);

        $this->assertEquals('F-|P-||-X|-L', $page->render());
    }

    function testDisabledTagsHidden()
    {
        $template = '{{pager id="test_pager" items="1" total_items="$#items_count"}}' .
            '{{pager:first:disabled}}F-|{{/pager:first:disabled}}' .
            '{{pager:prev:disabled}}P-|{{/pager:prev:disabled}}' .
            '{{pager:next:disabled}}|-X{{/pager:next:disabled}}' .
            '{{pager:last:disabled}}|-L{{/pager:last:disabled}}' .
            '{{/pager}}';

        $page = $this->_createMacroTemplate($template, 'tpl.html');
        $page->set('items_count', 3);

        $_GET['test_pager'] = 2;

        $this->assertEquals('', $page->render());
    }

    function testShowFirstAndPrevTagsDisabledOnly()
    {
        $template = '{{pager id="test_pager" items="10" total_items="$#items_count"}}' .
            '{{pager:first:disabled}}F-|{{/pager:first:disabled}}' .
            '{{pager:prev:disabled}}P-|{{/pager:prev:disabled}}' .
            '{{pager:next:disabled}}|-X{{/pager:next:disabled}}' .
            '{{pager:last:disabled}}|-L{{/pager:last:disabled}}' .
            '{{/pager}}';

        $page = $this->_createMacroTemplate($template, 'tpl.html');
        $page->set('items_count', 20);

        $this->assertEquals('F-|P-|', $page->render());
    }

    function testShowNextAndLastTagsDisabledOnly()
    {
        $template = '{{pager id="test_pager" items="10" total_items="$#items_count"}}' .
            '{{pager:first:disabled}}F-|{{/pager:first:disabled}}' .
            '{{pager:prev:disabled}}P-|{{/pager:prev:disabled}}' .
            '{{pager:next:disabled}}|-X{{/pager:next:disabled}}' .
            '{{pager:last:disabled}}|-L{{/pager:last:disabled}}' .
            '{{/pager}}';

        $page = $this->_createMacroTemplate($template, 'tpl.html');
        $page->set('items_count', 20);

        $_GET['test_pager'] = 2;

        $this->assertEquals('|-X|-L', $page->render());
    }
}
