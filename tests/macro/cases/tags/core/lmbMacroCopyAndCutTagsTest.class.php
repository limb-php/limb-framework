<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\macro\cases\tags\core;

use tests\macro\cases\lmbBaseMacroTest;

class lmbMacroCopyAndCutTagsTest extends lmbBaseMacroTest
{
  function testCopyTag()
  {
    $template = '{{copy into="$#my_buffer"}}F|{{/copy}}N|{$#my_buffer}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $this->assertEquals($page->render(), 'F|N|F|');
  }

  function testCutTag()
  {
    $template = '{{cut into="$#my_buffer"}}F|{{/cut}}N|{$#my_buffer}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $this->assertEquals($page->render(), 'N|F|');
  }
}

