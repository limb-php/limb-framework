<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\config\cases;

require_once '.setup.php';

use PHPUnit\Framework\TestCase;
use limb\config\src\lmbIni;
use limb\fs\src\lmbFs;

class lmbIniOverrideTest extends TestCase
{
    function setUp(): void
    {
        lmbFs::mkdir(lmb_var_dir() . '/tmp_ini');
    }

    function tearDown(): void
    {
        lmbFs::rm(lmb_var_dir() . '/tmp_ini');
    }

    function _createIniFileNames()
    {
        $name = mt_rand();
        $file = lmb_var_dir() . '/tmp_ini/' . $name . '.ini';
        $override_file = lmb_var_dir() . '/tmp_ini/' . $name . '.override.ini';
        return array($file, $override_file);
    }

    function testOverride()
    {
        list($file, $override_file) = $this->_createIniFileNames();

        file_put_contents($file,
            '[Templates]
        conf = 1
        force_compile = 0
        path = design/templates/');

        file_put_contents($override_file,
            '[Templates]
        conf =
        force_compile = 1');

        $ini = new lmbIni($file);

        $this->assertEquals($ini->getOption('conf', 'Templates'), null);
        $this->assertEquals($ini->getOption('path', 'Templates'), 'design/templates/');
        $this->assertEquals($ini->getOption('force_compile', 'Templates'), 1);
    }
}
