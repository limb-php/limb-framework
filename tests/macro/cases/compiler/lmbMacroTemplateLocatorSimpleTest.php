<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\compiler;

use tests\macro\cases\lmbBaseMacroTestCase;
use limb\macro\src\lmbMacroTemplateLocatorSimple;
use limb\macro\src\lmbMacroException;
use limb\macro\src\lmbMacroTemplate;

class lmbMacroTemplateLocatorSimpleTest extends lmbBaseMacroTestCase
{
    public $template_name = 'foo.phtml';

    function testlocateSourceTemplate()
    {
        $config = $this->_createMacroConfig();
        $config->tpl_scan_dirs = array($config->tpl_scan_dirs[0]);

        $template_locator = new lmbMacroTemplateLocatorSimple($config);
        try {
            $template = $template_locator->locateSourceTemplate($this->template_name);
            $this->fail();
        } catch (lmbMacroException $e) {
            $this->assertTrue(true);
        }

        $this->_createMacroTemplate('bar', $this->template_name);

        try {
            $template = $template_locator->locateSourceTemplate($this->template_name);
            $this->assertTrue(true);
        } catch (lmbMacroException $e) {
            $this->fail();
        }

        $this->assertEquals('bar', file_get_contents($template));
    }

    function testLocateCompiledTemplate()
    {
        $template_locator = new lmbMacroTemplateLocatorSimple($config = $this->_createMacroConfig());
        $compiled_file_name = lmbMacroTemplate::encodeCacheFileName($this->template_name);
        file_put_contents($config->cache_dir . '/' . $compiled_file_name, 'bar');

        $template = $template_locator->locateCompiledTemplate($this->template_name);

        $this->assertEquals('bar', file_get_contents($template));
    }

}
