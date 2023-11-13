<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace Tests\web_app\cases\plain\macro;

use limb\core\src\lmbEnv;
use limb\fs\src\lmbFs;
use limb\core\src\exception\lmbException;
use Tests\view\lmbMacroTestCase;

require_once dirname(__FILE__) . '/.setup.php';

class lmbJsRequireOnceMacroTagTest extends lmbMacroTestCase
{
  function testOnceRender()
  {
    lmbEnv::set('LIMB_DOCUMENT_ROOT', lmbEnv::get('LIMB_VAR_DIR').'/www');
    lmbFs::safeWrite(lmbEnv::get('LIMB_VAR_DIR') . '/www/js/main.js', 'function() { alert(1); }');
    $template = '{{js:require_once src="js/main.js" }}{{js_once src="js/main.js" }}';

    $page = $this->_createMacroTemplate($template, 'tpl.html'); 

    $content = '<script type="text/javascript" src="'.$this->toolkit->addVersionToUrl('js/main.js').'" ></script>';
    $this->assertEquals($content, $page->render());
  }

  function testNotFoundFile()
  {
    lmbEnv::set('LIMB_DOCUMENT_ROOT', lmbEnv::get('LIMB_VAR_DIR'));
    
    $template = '{{js:require_once src="js/main.js" }}';
    $page = $this->_createMacroTemplate($template, 'tpl.html'); 
   
    try
    {
      $page->render();
      $this->assertTrue(false);
    } 
    catch(lmbException $e)
    {
      $this->assertTrue(true);
    }
    
    $template = '{{js:require_once src="js/main.js" safe="true" }}';
    $page = $this->_createMacroTemplate($template, 'tpl.html'); 
   
    try
    {
      $page->render();
      $this->assertTrue(true);
    } 
    catch(lmbException $e)
    {
      $this->assertTrue(false);
    }
  }
}
