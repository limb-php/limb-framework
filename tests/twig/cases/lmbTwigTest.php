<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\twig\cases;

require_once '.setup.php';

use limb\twig\src\lmbTwigExtension;
use PHPUnit\Framework\TestCase;
use limb\fs\src\lmbFs;
use limb\core\src\lmbEnv;

class lmbTwigTest extends TestCase
{
    protected $tmp_dir;

    function setUp(): void
    {
        $this->tmp_dir = lmbEnv::get('LIMB_VAR_DIR') . '/tpl/';

        lmbFs::rm($this->tmp_dir);
        lmbFs::mkdir($this->tmp_dir);
    }

    function testRenderSimpleVars()
    {
        $template_name = 'RenderSimpleVars.twig';
        $this->_createTemplate("{{ number|number_format(2, '.') }}", $template_name);

        $twig = $this->_createView();
        $result = $twig->render($template_name, ['number' => 10547765]);

        $this->assertEquals('10,547,765.00', $result);
    }

    protected function _createTemplate($code, $name)
    {
        $file = $this->tmp_dir . $name;
        file_put_contents($file, $code);
        return $file;
    }

    protected function _createView($args = [])
    {
        $loader = new \Twig\Loader\FilesystemLoader([$this->tmp_dir]);

        $twig = new \Twig\Environment($loader, []);

        $twig->addExtension(new lmbTwigExtension());

        return $twig;
    }
}
