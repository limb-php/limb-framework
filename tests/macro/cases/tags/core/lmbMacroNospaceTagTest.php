<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace Tests\macro\cases\tags\core;

use Tests\macro\cases\lmbBaseMacroTestCase;

class lmbMacroNospaceTagTest extends lmbBaseMacroTestCase
{
  function testNospace()
  {
    $template = " Todd {{-
    
    }} Bob {{-
    
    }}Hey\n Tomm";

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $this->assertEquals(" Todd  Bob Hey\n Tomm", $page->render());
  }

  function testTrimSpace()
  {
    $template = '{{trim}}   Bob    {{/trim}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $this->assertEquals('Bob', $page->render());
  }

  function testMixTrimAndNoTrim()
  {
    $template = ' Todd {{trim}}   Bob    {{/trim}} Hey';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $this->assertEquals(' Todd Bob Hey', $page->render());
  }

  function testSpace()
  {
    $template = '{{trim}}{{sp}}Bob{{sp}}{{/trim}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $this->assertEquals(' Bob ', $page->render());
  }

  function testNewline()
  {
    $template = '{{trim}}{{nl}}Bob{{nl}}{{/trim}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $this->assertEquals("\nBob\n", $page->render());
  }

  function testTab()
  {
    $template = '{{trim}}{{tab}}Bob{{tab}}{{/trim}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $this->assertEquals("\tBob\t", $page->render());
  }
}
