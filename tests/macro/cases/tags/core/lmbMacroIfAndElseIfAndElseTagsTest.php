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
use limb\macro\src\lmbMacroException;

class lmbMacroIfAndElseIfAndElseTagsTest extends lmbBaseMacroTestCase
{
  function testIfTag()
  {
    $template = '{{if var="$#foo"}}A{{/if}}';
    $page = $this->_createMacroTemplate($template, 'tpl.html');

    $page->set('foo', true);
    $this->assertEquals('A', $page->render());

    $page->set('foo', false);
    $this->assertEquals('', $page->render());
  }

  function testIfTag_AttrAlias()
  {
    $template = '{{if expr="$#foo"}}A{{/if}}';
    $page = $this->_createMacroTemplate($template, 'tpl.html');

    $page->set('foo', true);
    $this->assertEquals('A', $page->render());

    $page->set('foo', false);
    $this->assertEquals('', $page->render());
  }

  function testIfTag_MissedAttr()
  {
    $template = '{{if}}A{{/if}}';
    $page = $this->_createMacroTemplate($template, 'tpl.html');
    try
    {
      $page->render();
      $this->fail();
    }
    catch(lmbMacroException $e)
    {
        $this->assertTrue(true);
    }   
  }

  function testElseIfTag()
  {
    $template = '{{if var="$#foo==1"}}A{{elseif var="$#foo==2"}}B{{/if}}';
    $page = $this->_createMacroTemplate($template, 'tpl.html');

    $page->set('foo', 1);
    $this->assertEquals('A', $page->render());

    $page->set('foo', 2);
    $this->assertEquals('B', $page->render());
  }

  function testElseIfTag_WithoutIf()
  {
    $page = $this->_createMacroTemplate('{{elseif var="without_if"}}', 'tpl.html');
    try
    {
      $page->render();
      $this->fail();
    }
    catch(lmbMacroException $e)
    {
        $this->assertTrue(true);
    }
  }

  function testElseIfTag_AttrAlias()
  {
    $template = '{{if var="$#foo==1"}}A{{elseif expr="$#foo==2"}}B{{/if}}';
    $page = $this->_createMacroTemplate($template, 'tpl.html');

    $page->set('foo', 2);
    $this->assertEquals('B', $page->render());
  }

  function testElseIfTag_MissedAttr()
  {
    $template = '{{if}}A{{elseif}}{{/if}}';
    $page = $this->_createMacroTemplate($template, 'tpl.html');
    try
    {
      $page->render();
      $this->fail();
    }
    catch(lmbMacroException $e)
    {
        $this->assertTrue(true);
    }   
  }


  function testElseTag()
  {
    $template = '{{if var="$#foo"}}A{{else}}B{{/if}}';
    $page = $this->_createMacroTemplate($template, 'tpl.html');

    $page->set('foo', true);
    $this->assertEquals('A', $page->render());

    $page->set('foo', false);
    $this->assertEquals('B', $page->render());
  }

  function testElseTag_WithoutIf()
  {
    $page = $this->_createMacroTemplate('{{else}}', 'tpl.html');
    try
    {
      $page->render();
      $this->fail();
    }
    catch(lmbMacroException $e)
    {
        $this->assertTrue(true);
    }
  }

  function testAcceptance()
  {
    $template = '{{if var="$#foo==1"}}A{{elseif var="$#foo==2"}}B{{else}}C{{/if}}';
    $page = $this->_createMacroTemplate($template, 'tpl.html');

    $page->set('foo', 1);
    $this->assertEquals('A', $page->render());

    $page->set('foo', 2);
    $this->assertEquals('B', $page->render());

    $page->set('foo', 3);
    $this->assertEquals('C', $page->render());
  }
}
