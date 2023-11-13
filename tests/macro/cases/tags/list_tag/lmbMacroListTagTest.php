<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace Tests\macro\cases\tags\list_tag;

use Tests\macro\cases\lmbBaseMacroTestCase;
use limb\core\src\lmbArrayIterator;

class lmbMacroListTagTest extends lmbBaseMacroTestCase
{
  function testSimpleList()
  {
    $list = '{{list using="$#list" as="$item"}}{{list:item}}<?=$item?> {{/list:item}}{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('Bob', 'Todd'));

    $out = $macro->render();
    $this->assertEquals('Bob Todd ', $out);
  }

  function testGroupVisibilityConditionForPreAndPostListTags()
  {
    $list = '{{list using="$#list" as="$item"}}<?if(false){?>Junk1<?}?>{{list:item}}<?=$item?> {{/list:item}}<?if(false){?>Junk2<?}?>{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('Bob', 'Todd'));

    $out = $macro->render();
    $this->assertEquals('Bob Todd ', $out);
  }

  function testListUsingDefaultItem()
  {
    $list = '{{list using="$#list"}}{{list:item}}<?=$item?> {{/list:item}}{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('Bob', 'Todd'));

    $out = $macro->render();
    $this->assertEquals('Bob Todd ', $out);
  }

  function testEmptyList()
  {
    $list = '{{list using="$#list" as="$item"}}{{list:item}}<?=$item?>{{/list:item}}' .
            '{{list:empty}}Nothing{{/list:empty}}{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array());

    $out = $macro->render();
    $this->assertEquals('Nothing', $out);
  }

  function testShowCounter()
  {
    $list = '{{list using="$#list" counter="$ctr"}}{{list:item}}<?=$ctr?>)<?=$item?> {{/list:item}}{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('Bob', 'Todd'));

    $out = $macro->render();
    $this->assertEquals('1)Bob 2)Todd ', $out);
  }

  function testTextNodesInsideListTag()
  {
    $list = '{{list using="$#list" as="$item"}}List: {{list:item}}<?=$item?> {{/list:item}} !{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('Bob', 'Todd'));

    $out = $macro->render();
    $this->assertEquals('List: Bob Todd  !', $out);
  }

  function testTextNodesInsideListTagWithEmptyListTag()
  {
    $list = '{{list using="$#list" as="$item"}}List: {{list:item}}<?=$item?> {{/list:item}} !' .
            '{{list:empty}}Nothing{{/list:empty}}{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array());

    $out = $macro->render();
    $this->assertEquals('Nothing', $out);
  }

  function testKeyValue()
  {
    $list = '{{list using="$#list" as="$item" key="$name"}}{{list:item}}{$name} is {$item}, {{/list:item}}{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('Bob' => 'god', 'Todd' => 'odd', 'zero', 1 => 'one'));

    $out = $macro->render();
    $this->assertEquals('Bob is god, Todd is odd, 0 is zero, 1 is one, ', $out);
  }
  
  function testParity()
  {
    $list = '{{list using="$#list" as="$item" parity="$parity"}}{{list:item}}{$parity}-{$item} {{/list:item}} !{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('Bob', 'Todd', 'Jeff'));

    $out = $macro->render();
    $this->assertEquals('odd-Bob even-Todd odd-Jeff  !', $out);
  }

  function testEvenAndOddTags()
  {
    $list = '{{list using="$#list" as="$item" parity="$parity"}}{{list:item}}'.
              '{{list:odd}}Odd{{/list:odd}}{{list:even}}Even{{/list:even}}-{$item} {{/list:item}} !{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('Bob', 'Todd', 'Jeff'));

    $out = $macro->render();
    $this->assertEquals('Odd-Bob Even-Todd Odd-Jeff  !', $out);
  }

  function testListWithGlue()
  {
    $list = '{{list using="$#list" as="$item"}}List:'.
            '{{list:item}}<?=$item?>{{list:glue}}||{{/list:glue}}'.
            '{{/list:item}}!' .
            '{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('Bob', 'Todd', 'Marry'));

    $out = $macro->render();
    $this->assertEquals('List:Bob||Todd||Marry!', $out);
  }

  function testListWithGlueAndKey()
  {
    $list = '{{list using="$#list" as="$item" key="$field"}}'.
            '{{list:item}}<?=$field?>:<?=$item?>{{list:glue}},{{/list:glue}}'.
            '{{/list:item}}!' .
            '{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('login' => 'exists', 'password' => 'required', 'email' => 'not_valid'));

    $out = $macro->render();
    $this->assertEquals('login:exists,password:required,email:not_valid!', $out);
  }

  function testListWithGlueWithStep()
  {
    $list = '{{list using="$#list" as="$item"}}List:'.
            '{{list:item}}<?=$item?>{{list:glue step="2"}}||{{/list:glue}}'.
            '{{/list:item}}!' .
            '{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('Bob', 'Todd', 'Marry'));

    $out = $macro->render();
    $this->assertEquals('List:BobTodd||Marry!', $out);
  }

  function testListWithGlueWithStepAsVariablee()
  {
    $list = '{{list using="$#list" as="$item"}}List:'.
            '{{list:item}}<?=$item?>{{list:glue step="{$#var_step}"}}||{{/list:glue}}'.
            '{{/list:item}}!' .
            '{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('Bob', 'Todd', 'Marry'));
    $macro->set('var_step', 2);

    $out = $macro->render();
    $this->assertEquals('List:BobTodd||Marry!', $out);
  }

  function testTwoDependentGlues()
  {
    $list = '{{list using="$#list" as="$item"}}List#'.
            '{{list:item}}<?=$item?>' .
            '{{list:glue step="2"}}|{{/list:glue}}'.
            '{{list:glue}}:{{/list:glue}}'.
            '{{/list:item}}!'.
            '{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('John', 'Pavel', 'Peter', 'Harry', 'Roman', 'Sergey'));

    $this->assertEquals('List#John:Pavel|Peter:Harry|Roman:Sergey!', $macro->render());
  }

  function testIndependentGlue()
  {
    $list = '{{list using="$#list" as="$item"}}List#'.
            '{{list:item}}<?=$item?>' .
            '{{list:glue step="2" independent="true"}}:{{/list:glue}}'.
            '{{list:glue step="3"}}|{{/list:glue}}'.
            '{{/list:item}}!'.
            '{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('John', 'Pavel', 'Peter', 'Harry', 'Roman', 'Sergey', 'Alex', 'Vlad'));

    $this->assertEquals('List#JohnPavel:Peter|Harry:RomanSergey:|AlexVlad!', $macro->render());
  }

  function testTwoGluesInsideNestingLists()
  {
    $list = '{{list using="$#list1" as="$item1"}}'.
            '{{list:item}}'.
              '{{list using="$#list2" as="$item2"}}'.
              '{{list:item}}'.            
              '<?=$item1?>' . '<?=$item2?>' .
              '{{list:glue}} - {{/list:glue}}'.
              '{{/list:item}}'.
              '{{/list}}' .
            '{{list:glue}}:{{/list:glue}}'.
            '{{/list:item}}'.
            '{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list1', array('X', 'Y'));
    $macro->set('list2', array('A', 'B'));

    $this->assertEquals('XA - XB:YA - YB', $macro->render());
  }
  
  function testListFillTagWithRatio()
  {
    $list = '{{list using="$#list" as="$item"}}List#'.
                '{{list:item}}{$item}'.
                '{{list:glue step="3"}}++{{/list:glue}}'.
                '{{list:glue}}:{{/list:glue}}'.
                '{{/list:item}}'.
                '{{list:fill upto="3" items_left="$items_left"}}{$items_left}{{/list:fill}}'.
                '{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('John', 'Pavel', 'Peter', 'Harry'));

    $this->assertEquals('List#John:Pavel:Peter++Harry2', $macro->render());
  }

  function testListFillTagWithTotalElementsLessThanRatioDoesNotRenderAnything()
  {
    $list = '{{list using="$#list" as="$item"}}List#'.
                '{{list:item}}{$item}'.
                '{{list:glue step="3"}}++{{/list:glue}}'.
                '{{list:glue}}:{{/list:glue}}'.
                '{{/list:item}}'.
                '{{list:fill upto="3" items_left="$items_left"}}{$items_left}{{/list:fill}}'.
                '{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('John', 'Pavel'));

    $this->assertEquals('List#John:Pavel', $macro->render());
  }

  function testListFillTagWithTotalElementsLessButWithForceAttributeIsRendering()
  {
    $list = '{{list using="$#list" as="$item"}}List#'.
            '{{list:item}}{$item}'.
            '{{list:glue step="3"}}++{{/list:glue}}'.
            '{{list:glue}}:{{/list:glue}}'.
            '{{/list:item}}'.
            '{{list:fill upto="3" force="true" items_left="$items_left"}}{$items_left}{{/list:fill}}'.
            '{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array('John', 'Pavel'));

    $this->assertEquals('List#John:Pavel1', $macro->render());
  }

  function testListFillTagWithTotalElementsLessButWithForceAttributeButWithEmptyList()
  {
    $list = '{{list using="$#list" as="$item"}}List#'.
            '{{list:item}}{$item}'.
            '{{list:glue step="3"}}++{{/list:glue}}'.
            '{{list:glue}}:{{/list:glue}}'.
            '{{/list:item}}'.
            '{{list:fill upto="3" force="true" items_left="$items_left"}}{$items_left}{{/list:fill}}'.
            '{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $macro->set('list', array());

    $this->assertEquals('', $macro->render());
  }
  
  
  function testListFillTag_WithoutGlueTag_AndListHasTheSameNumberOfItemsAndFillTagUpTo()
  {
    $list = '{{list using="$#list" as="$item"}}List#'.
                '{{list:item}}{$item}'.
                '{{/list:item}}'.
                '{{list:fill upto="3" items_left="$items_left"}}{$items_left}{{/list:fill}}'.
                '{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);

    $list = new lmbArrayIterator(array('John', 'Pavel', 'Serega', 'Viktor'));
    $list->paginate(0, 3);
    $macro->set('list', $list);

    $this->assertEquals('List#JohnPavelSerega', $macro->render());
  }  
  
  function testIterationUsingIteratorAggregate()
  {
    $list = '{{list using="$#list" as="$item"}}{{list:item}}<?=$item?> {{/list:item}}{{/list}}';

    $list_tpl = $this->_createTemplate($list, 'list.html');

    $macro = $this->_createMacro($list_tpl);
    $array = new \ArrayObject(array('Bob', 'Todd'));
    $macro->set('list', $array);

    $out = $macro->render();
    $this->assertEquals('Bob Todd ', $out);
  }
}
