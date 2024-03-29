<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\web_app\cases\plain\macro;

use limb\core\src\lmbEnv;
use limb\fs\src\lmbFs;
use limb\core\src\exception\lmbException;
use tests\view\lmbMacroTestCase;

require_once dirname(__FILE__) . '/../../init.inc.php';

class lmbJsCombinedMacroTagTest extends lmbMacroTestCase
{
    function testRender()
    {
        $root = lmbEnv::get('LIMB_VAR_DIR') . '/www/';
        lmbEnv::set('LIMB_DOCUMENT_ROOT', $root);
        lmbFs::safeWrite($root . 'js/main.js', 'content main.js');
        lmbFs::safeWrite($root . 'js/blog.js', 'is blog.js');

        lmbFs::rm($root . '/media/var');

        $template = '
    {{js:combined dir="media/var"}}
      {{js_once src="js/main.js" }}
      {{js_once src="js/blog.js" }}
    {{/js:combined}}
    ';

        $page = $this->_createMacroTemplate($template, 'tpl.html');
        $content = trim($page->render());
        $ls = lmbFs::ls($root . '/media/var/');
        $file = array_shift($ls);

        $this->assertEquals('<script type="text/javascript" src="' . $this->toolkit->addVersionToUrl('media/var/' . $file) . '" ></script>', $content);

        $js_content =
            "/* include main.js */\n" .
            "content main.js\n" .
            "/* include blog.js */\n" .
            "is blog.js";
        $this->assertEquals(file_get_contents($root . '/media/var/' . $file), $js_content);
    }

    function testFileNameNotDependOrderFiles()
    {
        lmbEnv::set('LIMB_DOCUMENT_ROOT', ($root = lmbEnv::get('LIMB_VAR_DIR') . '/www/'));
        lmbFs::safeWrite($root . 'js/main.js', 'content');
        lmbFs::safeWrite($root . 'js/blog.js', 'content');

        lmbFs::rm($root . '/media/var');

        $template = '
    {{js:combined dir="media/var"}}
      {{js_once src="js/main.js" }}
      {{js_once src="js/blog.js" }}
    {{/js:combined}}';

        $this->_createMacroTemplate($template, 'tpl.html')->render();
        $ls = lmbFs::ls($root . '/media/var/');
        $file_name_one = array_shift($ls);

        $template = '
    {{js:combined dir="media/var"}}
      {{js_once src="js/blog.js" }}
      {{js_once src="js/main.js" }}
    {{/js:combined}}';

        $this->_createMacroTemplate($template, 'tpl.html')->render();
        $ls = lmbFs::ls($root . '/media/var/');
        $file_name_two = array_shift($ls);

        $this->assertEquals($file_name_one, $file_name_two);
    }

    function testNotFoundFile()
    {
        $root = lmbEnv::get('LIMB_VAR_DIR') . '/www';
        lmbEnv::set('LIMB_DOCUMENT_ROOT', $root);
        lmbFs::rm($root);

        $template = '{{js_combined dir="media/"}}{{js_once src="js/main.js" }}{{/js_combined}}';
        $page = $this->_createMacroTemplate($template, 'tpl.html');

        try {
            $page->render();
            $this->fail();
        } catch (lmbException $e) {
            $this->assertTrue(true);
        }

        lmbFs::safeWrite($root . '/js/blog.js', 'function blog() {};');

        $template = '{{js_combined dir="media"}}{{js_once src="js/main.js" safe="true" }}{{js_once src="js/blog.js" }}{{/js_combined}}';
        $page = $this->_createMacroTemplate($template, 'tpl.html');
        $page->render();

        $ls = lmbFs::ls($root . '/media/');
        $file = array_shift($ls);

        $js_content = "/* include main.js - NOT FOUND */\n\n/* include blog.js */\nfunction blog() {};";
        $this->assertEquals(file_get_contents($root . '/media/' . $file), $js_content);
    }
}
