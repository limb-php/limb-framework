<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\tags\core;

use tests\macro\cases\lmbBaseMacroTestCase;

class lmbMacroWrapTagBCTest extends lmbBaseMacroTestCase
{
    function testSimpleStaticWrap()
    {
        $bar = '{{wrap with="foo.html" into="slot1"}}Bob{{/wrap}}';
        $foo = '<p>Hello, {{slot id="slot1"/}}</p>';

        $bar_tpl = $this->_createTemplate($bar, 'bar.html');
        $foo_tpl = $this->_createTemplate($foo, 'foo.html');

        $macro = $this->_createMacro($bar_tpl);

        $out = $macro->render();
        $this->assertEquals('<p>Hello, Bob</p>', $out);
    }

    function testStaticWrapWithVariables()
    {
        $bar = '{{wrap with="foo.html" into="slot1"}}<?php echo $this->bob?>{{/wrap}}';
        $foo = '<p>Hello, {{slot id="slot1"/}}</p>';

        $bar_tpl = $this->_createTemplate($bar, 'bar.html');
        $foo_tpl = $this->_createTemplate($foo, 'foo.html');

        $macro = $this->_createMacro($bar_tpl);
        $macro->set('bob', 'Bob');

        $out = $macro->render();
        $this->assertEquals('<p>Hello, Bob</p>', $out);
    }

    function testNestedStaticWrap()
    {
        $bar = '{{wrap with="foo.html" into="slot1"}}<?php echo $this->bob?>{{/wrap}}';
        $foo = '{{wrap with="zoo.html" into="slot2"}}<p>Hello, {{slot id="slot1"/}}</p>{{/wrap}}';
        $zoo = '<body>{{slot id="slot2"/}}</body>';

        $bar_tpl = $this->_createTemplate($bar, 'bar.html');
        $foo_tpl = $this->_createTemplate($foo, 'foo.html');
        $zoo_tpl = $this->_createTemplate($zoo, 'zoo.html');

        $macro = $this->_createMacro($bar_tpl);
        $macro->set('bob', 'Bob');

        $out = $macro->render();
        $this->assertEquals('<body><p>Hello, Bob</p></body>', $out);
    }

    function testSimpleStaticIntoRoot()
    {
        $included = '{{into slot="slot1"}}Bob{{/into}}';
        $main = '<p>Hello, {{slot id="slot1"/}}</p>{{include file="included.html"/}}';

        $included_tpl = $this->_createTemplate($included, 'included.html');
        $main_tpl = $this->_createTemplate($main, 'main.html');

        $macro = $this->_createMacro($main_tpl);

        $out = $macro->render();
        $this->assertEquals('<p>Hello, Bob</p>', $out);
    }

    function testMultiStaticWrap()
    {
        $bar = '{{wrap with="foo.html"}}{{into slot="slot1"}}Bob{{/into}}{{into slot="slot2"}}Thorton{{/into}}{{/wrap}}';
        $foo = '<p>Hello, {{slot id="slot2"/}} {{slot id="slot1"/}}</p>';

        $bar_tpl = $this->_createTemplate($bar, 'bar.html');
        $foo_tpl = $this->_createTemplate($foo, 'foo.html');

        $macro = $this->_createMacro($bar_tpl);

        $out = $macro->render();
        $this->assertEquals('<p>Hello, Thorton Bob</p>', $out);
    }

    function testSimpleDynamicWrap()
    {
        $bar = '{{wrap with="$this->layout" into="slot1"}}Bob{{/wrap}}';
        $foo = '<p>Hello, {{slot id="slot1"/}}</p>';

        $bar_tpl = $this->_createTemplate($bar, 'bar.html');
        $foo_tpl = $this->_createTemplate($foo, 'foo.html');

        $macro = $this->_createMacro($bar_tpl);
        $macro->set('layout', 'foo.html');

        $out = $macro->render();
        $this->assertEquals('<p>Hello, Bob</p>', $out);
    }

    function testMultiDynamicWrap()
    {
        $bar = '{{wrap with="$this->layout"}}{{into slot="slot1"}}Bob{{/into}}{{into slot="slot2"}}Thorton{{/into}}{{/wrap}}';
        $foo = '<p>Hello, {{slot id="slot2"/}} {{slot id="slot1"/}}</p>';

        $bar_tpl = $this->_createTemplate($bar, 'bar.html');
        $foo_tpl = $this->_createTemplate($foo, 'foo.html');

        $macro = $this->_createMacro($bar_tpl);
        $macro->set('layout', 'foo.html');

        $out = $macro->render();
        $this->assertEquals('<p>Hello, Thorton Bob</p>', $out);
    }

    function testMixStaticAndDynamicWrap()
    {
        $bar = '{{wrap with="$this->layout" into="slot1"}}<?php echo $this->bob?>{{/wrap}}';
        $foo = '{{wrap with="zoo.html" into="slot2"}}<p>Hello, {{slot id="slot1"/}}</p>{{/wrap}}';
        $zoo = '<body>{{slot id="slot2"/}}</body>';

        $bar_tpl = $this->_createTemplate($bar, 'bar.html');
        $foo_tpl = $this->_createTemplate($foo, 'foo.html');
        $zoo_tpl = $this->_createTemplate($zoo, 'zoo.html');

        $macro = $this->_createMacro($bar_tpl);
        $macro->set('layout', 'foo.html');
        $macro->set('bob', 'Bob');

        $out = $macro->render();
        $this->assertEquals('<body><p>Hello, Bob</p></body>', $out);
    }

    function testNestedDynamicWrap()
    {
        $bar = '{{wrap with="$this->layout1" into="slot1"}}<?php echo $this->bob?>{{/wrap}}';
        $foo = '{{wrap with="$this->layout2" into="slot2"}}<p>Hello, {{slot id="slot1"/}}</p>{{/wrap}}';
        $zoo = '<body>{{slot id="slot2"/}}</body>';

        $bar_tpl = $this->_createTemplate($bar, 'bar.html');
        $foo_tpl = $this->_createTemplate($foo, 'foo.html');
        $zoo_tpl = $this->_createTemplate($zoo, 'zoo.html');

        $macro = $this->_createMacro($bar_tpl);
        $macro->set('layout1', 'foo.html');
        $macro->set('layout2', 'zoo.html');
        $macro->set('bob', 'Bob');

        $out = $macro->render();
        $this->assertEquals('<body><p>Hello, Bob</p></body>', $out);
    }

    function testStaticallyWrappedChildAccessesParentData()
    {
        $bar = '{{wrap with="foo.html" into="slot1"}}<?php echo $this->bob?>{{/wrap}}';
        $foo = '<?php $this->bob = "Bob";?><p>Hello, {{slot id="slot1"/}}</p>';

        $bar_tpl = $this->_createTemplate($bar, 'bar.html');
        $foo_tpl = $this->_createTemplate($foo, 'foo.html');

        $macro = $this->_createMacro($bar_tpl);

        $out = $macro->render();
        $this->assertEquals('<p>Hello, Bob</p>', $out);
    }

    function testDynamicallyWrappedChildAccessesParentData()
    {
        $bar = '{{wrap with="$this->layout" into="slot1"}}<?php echo $this->bob?>{{/wrap}}';
        $foo = '<?php $this->bob = "Bob";?><p>Hello, {{slot id="slot1"/}}</p>';

        $bar_tpl = $this->_createTemplate($bar, 'bar.html');
        $foo_tpl = $this->_createTemplate($foo, 'foo.html');

        $macro = $this->_createMacro($bar_tpl);
        $macro->set('layout', 'foo.html');

        $out = $macro->render();
        $this->assertEquals('<p>Hello, Bob</p>', $out);
    }

    function testStaticallyWrappedChildLocalVarsAreIsolated()
    {
        $bar = '{{wrap with="foo.html" into="slot1"}}<?php $foo = "Todd";?>{{/wrap}}';
        $foo = '<?php $foo = "Bob";?>{{slot id="slot1"/}}<?php echo $foo;?>';

        $bar_tpl = $this->_createTemplate($bar, 'bar.html');
        $foo_tpl = $this->_createTemplate($foo, 'foo.html');

        $macro = $this->_createMacro($bar_tpl);

        $out = $macro->render();
        $this->assertEquals('Bob', $out);
    }

    function testDynamicallyWrappedChildLocalVarsAreIsolated()
    {
        $bar = '{{wrap with="$this->layout" into="slot1"}}<?php $foo = "Todd";?>{{/wrap}}';
        $foo = '<?php $foo = "Bob";?>{{slot id="slot1"/}}<?php echo $foo;?>';

        $bar_tpl = $this->_createTemplate($bar, 'bar.html');
        $foo_tpl = $this->_createTemplate($foo, 'foo.html');

        $macro = $this->_createMacro($bar_tpl);
        $macro->set('layout', 'foo.html');

        $out = $macro->render();
        $this->assertEquals('Bob', $out);
    }

    function testMultiStaticWrapFromIncludedFile()
    {
        $child = '{{into slot="slot1"}}Bob{{/into}}{{into slot="slot2"}}Thorton{{/into}}';
        $main = '{{wrap with="base.html"}}{{include file="child.html"/}}{{/wrap}}';
        $base = '<p>Hello, {{slot id="slot2"/}} {{slot id="slot1"/}}</p>';

        $child_tpl = $this->_createTemplate($child, 'child.html');
        $base_tpl = $this->_createTemplate($base, 'base.html');
        $main_tpl = $this->_createTemplate($main, 'main.html');

        $macro = $this->_createMacro($main_tpl);

        $out = $macro->render();
        $this->assertEquals('<p>Hello, Thorton Bob</p>', $out);
    }

    function testPassVariablesIntoLocalContextOfSlotTag()
    {
        $bar = '{{wrap with="foo.html" into="slot1"}}<?php echo $foo;?>{{/wrap}}';
        $foo = '<?php $foo = "Bob";?>{{slot id="slot1" foo="$foo"/}}';

        $bar_tpl = $this->_createTemplate($bar, 'bar.html');
        $foo_tpl = $this->_createTemplate($foo, 'foo.html');

        $macro = $this->_createMacro($bar_tpl);

        $out = $macro->render();
        $this->assertEquals('Bob', $out);
    }

    function testSlotWithInlineAttributeDoesNotCreateAMethodAround()
    {
        $bar = '{{wrap with="foo.html" into="slot1"}}<?php $foo = "Tedd";?>{{/wrap}}';
        $foo = '<?php $foo = "Bob";?>{{slot id="slot1" inline="true"/}}<?php echo $foo;?>';

        $bar_tpl = $this->_createTemplate($bar, 'bar.html');
        $foo_tpl = $this->_createTemplate($foo, 'foo.html');

        $macro = $this->_createMacro($bar_tpl);

        $out = $macro->render();
        $this->assertEquals('Tedd', $out);
    }

    function testMixDynamicWrapWithStaticIncludeWithChildIntoTags()
    {
        $layout = '<body>Main: {{slot id="slot_main"/}} Extra: {{slot id="slot_extra"/}}</body>';

        $bar = '{{wrap with="$#layout"}}' .
            '{{wrap:into slot="slot_main"}}<?php $var2=2;?>' .
            '{{include file="foo.html" var1="1" var2="$var2"}}' .
            '{{include:into slot="slot1"}}<b><?php echo $varA;?></b>{{/include:into}}' .
            '{{include:into slot="slot2"}}<u><?php echo $varB;?></u>{{/include:into}}' .
            '{{/include}}' .
            '{{/wrap:into}}' .
            '{{/wrap}}';

        $foo = '<p>Numbers: {{slot id="slot1" varA="$var1"/}} {{slot id="slot2" varB="$var2"/}}</p> ' .
            '{{wrap:into slot="slot_extra"}}Wow!{{/wrap:into}}'; // !!!Note this wrap:into tag

        $bar_tpl = $this->_createTemplate($bar, 'bar.html');
        $foo_tpl = $this->_createTemplate($foo, 'foo.html');
        $layout_tpl = $this->_createTemplate($layout, 'layout.html');

        $macro = $this->_createMacro($bar_tpl);
        $macro->set('layout', 'layout.html');

        $out = $macro->render();
        $this->assertEquals('<body>Main: <p>Numbers: <b>1</b> <u>2</u></p>  Extra: Wow!</body>', $out);
    }

    function testIncludeAfterSlot()
    {
        $layout = 'Slot:{{slot id="content"/}} Include:{{include file="include.phtml"/}}';
        $content = '{{wrap with="layout.phtml" into="content"}}Hi!{{/wrap}}';
        $include = "Bob";

        $layout_tpl = $this->_createTemplate($layout, 'layout.phtml');
        $content_tpl = $this->_createTemplate($content, 'content.phtml');
        $include_tpl = $this->_createTemplate($include, 'include.phtml');

        $macro = $this->_createMacro($content_tpl);
        $out = $macro->render();
        $this->assertEquals('Slot:Hi! Include:Bob', $out);
    }
}
