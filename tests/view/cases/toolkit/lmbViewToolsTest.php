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

        $this->assertEquals(['.phtml', '.twig', '.php'], $exts);
    }

    function testLocateTemplateByAlias()
    {
        lmbFs::cp(__DIR__ . '/../template/index/view.php', $file = lmbEnv::get('LIMB_VAR_DIR') . '/index/view.php');

        $toolkit = lmbToolkit::instance();

        $filepath = $toolkit->locateTemplateByAlias('index/view', lmbPHPView::class);
        $this->assertEquals(lmbEnv::get('LIMB_VAR_DIR') . '/index/view.php', $filepath);

        lmbFs::rm($file);
    }

    function testCreateViewByTemplate()
    {
        $toolkit = lmbToolkit::instance();

        $result = $toolkit->createViewByTemplate('index/view.php');

        $this->assertEquals('Hello World!', $result);
    }

}
