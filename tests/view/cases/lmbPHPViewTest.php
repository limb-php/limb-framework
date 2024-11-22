<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\view\cases;

require_once (dirname(__FILE__) . '/init.inc.php');

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
        $this->assertEquals('Hello, world', $template->render());
        unlink($file);
    }
}
