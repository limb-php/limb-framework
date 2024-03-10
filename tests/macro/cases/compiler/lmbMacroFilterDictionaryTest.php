<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\compiler;

use limb\core\src\lmbEnv;
use tests\macro\cases\lmbBaseMacroTestCase;
use limb\fs\src\lmbFs;
use limb\macro\src\compiler\lmbMacroFilterInfo;
use limb\macro\src\compiler\lmbMacroFilterDictionary;

class lmbMacroFilterDictionaryTest extends lmbBaseMacroTestCase
{
    function setUp(): void
    {
        lmbFs::rm(lmbEnv::get('LIMB_VAR_DIR') . '/filters/');
        lmbFs::mkdir(lmbEnv::get('LIMB_VAR_DIR') . '/filters/');
    }

    function testFindFilterInfo()
    {
        $filter_info = new lmbMacroFilterInfo('testfilter', 'SomeFilterClass');
        $dictionary = new lmbMacroFilterDictionary();
        $dictionary->register($filter_info);

        $this->assertInstanceOf(lmbMacroFilterInfo::class, $dictionary->findFilterInfo('testfilter'));
    }

    function testFindFilterInfoByAlias()
    {
        $filter_info = new lmbMacroFilterInfo('testfilter', 'SomeFilterClass');
        $filter_info->setAliases(array('testfilter_alias', 'testfilter_alias2'));
        $dictionary = new lmbMacroFilterDictionary();
        $dictionary->register($filter_info);

        $this->assertInstanceOf(lmbMacroFilterInfo::class, $dictionary->findFilterInfo('testfilter'));
        $this->assertInstanceOf(lmbMacroFilterInfo::class, $dictionary->findFilterInfo('testfilter_alias'));
        $this->assertInstanceOf(lmbMacroFilterInfo::class, $dictionary->findFilterInfo('testfilter_alias2'));
    }

    function testRegisterFilterInfoOnceOnly()
    {
        $dictionary = new lmbMacroFilterDictionary();
        $filter_info1 = new lmbMacroFilterInfo('some_filter', 'SomeFilterClass');
        $filter_info2 = new lmbMacroFilterInfo('some_filter', 'SomeFilterClass');
        $dictionary->register($filter_info1);
        $dictionary->register($filter_info2);

        $this->assertEquals($dictionary->findFilterInfo('some_filter'), $filter_info1);
    }

    function testFilterNotFound()
    {
        $filter_info = new lmbMacroFilterInfo('testfilter', 'SomeFilterClass');
        $dictionary = new lmbMacroFilterDictionary();
        $dictionary->register($filter_info);

        $this->assertNull($dictionary->findFilterInfo('junk'));
    }

    function testRegisterFromFile()
    {
        $rnd = mt_rand();
        $contents = <<<EOD
<?php
/**
 * @filter foo_{$rnd}
 * @aliases foo1_{$rnd}, foo2_{$rnd} 
 */
class Foo{$rnd}Filter extends lmbMacroFilter{}

/**
 * @filter bar_{$rnd}
 */
class Bar{$rnd}Filter extends lmbMacroFilter{}
EOD;
        file_put_contents($file = lmbEnv::get('LIMB_VAR_DIR') . '/filters/' . $rnd . '.filter.php', $contents);

        $filter_info1 = new lmbMacroFilterInfo("foo_$rnd", "Foo{$rnd}Filter");
        $filter_info1->setAliases(array("foo1_$rnd", "foo2_$rnd"));
        $filter_info1->setFile($file);
        $filter_info2 = new lmbMacroFilterInfo("bar_$rnd", "Bar{$rnd}Filter");
        $filter_info2->setFile($file);

        $dictionary = new lmbMacroFilterDictionary();
        $dictionary->registerFromFile($file);

        $this->assertEquals($dictionary->findFilterInfo("foo_$rnd"), $filter_info1);
        $this->assertEquals($dictionary->findFilterInfo("foo1_$rnd"), $filter_info1);
        $this->assertEquals($dictionary->findFilterInfo("bar_$rnd"), $filter_info2);
    }
}
