<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace Tests\macro\cases\tags\core;

use tests\macro\cases\lmbBaseMacroTestCase;

class lmbMacroCurlyBracesTagTest extends lmbBaseMacroTestCase
{
  function testBraces()
  {
    $template = "{{cbo}}{{cbo}}macro{{cbc}}{{cbc}}";

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $this->assertEquals("{{macro}}", $page->render());
  }
}
