<?php

/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

use limb\core\src\lmbEnv;
use limb\fs\src\lmbFs;

class lmbFileVersionMacroTagTest extends lmbMacroTestCase
{
  function testRender()
  {
    lmbEnv::set('LIMB_DOCUMENT_ROOT', lmbEnv::get('LIMB_VAR_DIR').'/www');
    lmbFs::safeWrite(lmbEnv::get('LIMB_VAR_DIR') . '/www/index.html', '<html>Hello!</html>');

    $template = '{{file:version src="index.html" }}';

    $page = $this->_createMacroTemplate($template, 'tpl.html'); 

    $content = $this->toolkit->addVersionToUrl('index.html');
    $this->assertEquals($content, $page->render());
  }
  
  function testSafeAttribute()
  {
    lmbEnv::set('LIMB_DOCUMENT_ROOT', lmbEnv::get('LIMB_VAR_DIR').'/www/');
    lmbFs::rm(lmbEnv::get('LIMB_DOCUMENT_ROOT').'not_found.html');

    $template = '{{file:version src="not_found.html" }}';
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
    
    $template = '{{file:version src="not_found.html" safe="1" }}';
    $page = $this->_createMacroTemplate($template, 'tpl.html'); 

    $content = $this->toolkit->addVersionToUrl('not_found.html', true);
    $this->assertEquals($content, $page->render());
  }

  function testToVar()
  {
    lmbEnv::set('LIMB_DOCUMENT_ROOT', lmbEnv::get('LIMB_VAR_DIR').'/www');
    lmbFs::safeWrite(lmbEnv::get('LIMB_VAR_DIR') . '/www/index.html', '<html>Hello!</html>');

    $template = '{{file:version src="index.html" to_var="$one" }} -{$one}-';

    $page = $this->_createMacroTemplate($template, 'tpl.html'); 

    $this->assertEquals('-'.$this->toolkit->addVersionToUrl('index.html').'-', trim($page->render()));
  }

  function testGzipStatic()
  {
    if(!function_exists('gzencode'))
      return print("Skip: function gzencode not exists.\n");

    lmbEnv::set('LIMB_DOCUMENT_ROOT', lmbEnv::get('LIMB_VAR_DIR').'/www/');
    lmbFs::safeWrite(lmbEnv::get('LIMB_VAR_DIR') . '/www/one.js', 'var window = {};');
    $doc_root = lmbEnv::get('LIMB_DOCUMENT_ROOT');

    $template = '{{file:version src="one.js" gzip_static_dir="media/var/gz" gzip_level="9" }}';
    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $content = $page->render();
    $file = 'media/var/gz/one.js';
    $this->assertEquals($content, $this->toolkit->addVersionToUrl($file, false));
    $this->assertEquals('var window = {};', file_get_contents($doc_root . $file));
    $gz_file = $doc_root . $file . '.gz';
    $this->assertTrue(file_exists($gz_file));
    $this->assertEquals(gzencode('var window = {};', 9, FORCE_DEFLATE), file_get_contents($gz_file));
  }
}
