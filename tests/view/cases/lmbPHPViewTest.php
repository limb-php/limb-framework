<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\view\cases;

include_once '.setup.php';

use PHPUnit\Framework\TestCase;
use limb\view\src\lmbPHPView;
use limb\core\src\lmbEnv;

class lmbPHPViewTest extends TestCase
{
  function testRender()
  {
    file_put_contents($file = lmbEnv::get('LIMB_VAR_DIR') . '/tpl.php', '<?php echo "$msg, $name"; ?>');
    $template = new lmbPHPView($file);
    $template->set('msg', 'Hello');
    $template->set('name', 'world');
    $this->assertEquals($template->render(), 'Hello, world');
    unlink($file);
  }
}
