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

class lmbMacroRepeatTagTest extends lmbBaseMacroTestCase
{
  function testRepeatTimesIsStaticNumber()
  {
    $template = '{{repeat times="3"}}F{{/repeat}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $this->assertEquals('FFF', $page->render());
  }
  
  function testRepeatTimesIsVariableValue()
  {
    $template = '{{repeat times="$#count"}}F{{/repeat}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('count', 2);
    
    $this->assertEquals('FF', $page->render());
  }
}
