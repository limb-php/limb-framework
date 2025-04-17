<?php
/*
* Limb PHP Framework
*
* @link http://limb-project.com
* @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
* @license    LGPL http://www.gnu.org/copyleft/lesser.html
*/

namespace tests\view\cases\toolkit;

require_once (dirname(__FILE__) . '/../init.inc.php');

use limb\fs\src\lmbFs;
use limb\toolkit\src\lmbToolkit;
use limb\view\src\lmbPHPView;
use PHPUnit\Framework\TestCase;
use limb\core\src\lmbEnv;

class lmbViewToolsTest extends TestCase
{

    function testSupportedViewExtensions()
    {
        $toolkit = lmbToolkit::instance();

        $exts = $toolkit->getSupportedViewExtensions();

        $this->assertEquals(['.twig', '.php', '.phtml'], $exts);
    }

    function testLocateTemplateByAlias()
    {
        $dst_file = lmbEnv::get('LIMB_VAR_DIR') . '/index/view.php';
        lmbFs::cp(__DIR__ . '/../template/index/view.php', $dst_file);

        $toolkit = lmbToolkit::instance();
        $toolkit->setSupportedViewTypes(['.php' => lmbPHPView::class]);

        $filepath = $toolkit->locateTemplateByAlias('index/view.php', lmbPHPView::class);
        $this->assertEquals(lmbFs::normalizePath($dst_file), lmbFs::normalizePath($filepath));

        lmbFs::rm($dst_file);
    }

    function testCreateViewByTemplate()
    {
        $dst_file = lmbEnv::get('LIMB_VAR_DIR') . '/index/view.php';
        lmbFs::cp(__DIR__ . '/../template/index/view.php', $dst_file);

        $toolkit = lmbToolkit::instance();

        $view = $toolkit->createViewByTemplate('index/view.php');

        $toolkit->setSupportedViewTypes(['.php' => lmbPHPView::class]);

        $this->assertEquals('Hello World!', $view->render());
    }

}
