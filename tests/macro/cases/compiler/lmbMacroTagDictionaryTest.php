<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\compiler;

use tests\macro\cases\lmbBaseMacroTestCase;
use limb\macro\src\compiler\lmbMacroTagInfo;
use limb\macro\src\compiler\lmbMacroTagDictionary;
use limb\fs\src\lmbFs;
use limb\core\src\lmbEnv;

class lmbMacroTagDictionaryTest extends lmbBaseMacroTestCase
{
    function setUp(): void
    {
        parent::setUp();
        lmbFs::mkdir(lmbEnv::get('LIMB_VAR_DIR') . '/tpl/tags/');
        lmbFs::mkdir(lmbEnv::get('LIMB_VAR_DIR') . '/tpl/tags/subfolder/');
    }

    function testFindTagInfo()
    {
        $tag_info = new lmbMacroTagInfo('test_tag', 'SomeTagClass');
        $dictionary = new lmbMacroTagDictionary();
        $dictionary->register($tag_info, $file = 'whatever');

        $this->assertInstanceOf(lmbMacroTagInfo::class, $dictionary->findTagInfo('test_tag'));
    }

    function testFindFilterInfoByAlias()
    {
        $tag_info = new lmbMacroTagInfo('testtag', 'SomeFilterClass');
        $tag_info->setAliases(array('testtag_alias', 'testtag_alias2'));
        $dictionary = new lmbMacroTagDictionary();
        $dictionary->register($tag_info, $file = 'whatever');

        $this->assertInstanceOf(lmbMacroTagInfo::class, $dictionary->findTagInfo('testtag'));
        $this->assertInstanceOf(lmbMacroTagInfo::class, $dictionary->findTagInfo('testtag_alias'));
        $this->assertInstanceOf(lmbMacroTagInfo::class, $dictionary->findTagInfo('testtag_alias2'));
    }

    function testRegisterTagInfoOnceOnly()
    {
        $dictionary = new lmbMacroTagDictionary();
        $tag_info1 = new lmbMacroTagInfo('some_tag', 'SomeTagClass');
        $tag_info2 = new lmbMacroTagInfo('some_tag', 'SomeTagClass');
        $dictionary->register($tag_info1, $file1 = 'whatever1');
        $dictionary->register($tag_info2, $file2 = 'whatever2');

        $this->assertEquals($dictionary->findTagInfo('some_tag'), $tag_info1);
    }

    function testTagNotFound()
    {
        $tag_info = new lmbMacroTagInfo('testtag', 'SomeTagClass');
        $dictionary = new lmbMacroTagDictionary();
        $dictionary->register($tag_info, $file = 'whatever');

        $this->assertNull($dictionary->findTagInfo('junk'));
    }

    function testRegisterFromFile()
    {
        $rnd = mt_rand();
        $contents = <<<EOD
<?php
/**
 * @tag foo_{$rnd}
 * @req_attributes attr1, attr2
 * @restrict_self_nesting
 * @parent_tag_class SomeParentTagClass
 * @aliases foo1_{$rnd}, foo2_{$rnd} 
 * @forbid_end_tag
 */
class Foo{$rnd}Tag extends lmbMacroTag{}

/**
 * @tag bar_{$rnd}
 */
class Bar{$rnd}Tag extends lmbMacroTag{}
EOD;
        $config = $this->_createMacroConfig();
        $config->tags_scan_dirs = array($this->cache_dir);

        file_put_contents($file = $config->tags_scan_dirs[0] . $rnd . '.tag.php', $contents);

        $tag_info1 = new lmbMacroTagInfo("foo_$rnd", "Foo{$rnd}Tag");
        $tag_info1->setFile($file);
        $tag_info1->setForbidEndtag(true);
        $tag_info1->setAliases(array("foo1_$rnd", "foo2_$rnd"));
        $tag_info1->setRestrictSelfNesting(true);
        $tag_info1->setParentClass('SomeParentTagClass');
        $tag_info1->setRequiredAttributes(array('attr1', 'attr2'));

        $tag_info2 = new lmbMacroTagInfo("bar_$rnd", "Bar{$rnd}Tag");
        $tag_info2->setFile($file);

        $dictionary = new lmbMacroTagDictionary();
        $dictionary->registerFromFile($file);

        $this->assertEquals($dictionary->findTagInfo("foo_$rnd"), $tag_info1);
        $this->assertEquals($dictionary->findTagInfo("foo1_$rnd"), $tag_info1);
        $this->assertEquals($dictionary->findTagInfo("bar_$rnd"), $tag_info2);
    }

    function testLoad()
    {
        $rnd = mt_rand();
        $content1 = <<<EOD
<?php
/**
 * @tag foo_{$rnd}
 */
class Foo{$rnd}Tag extends lmbMacroTag{}
EOD;

        $content2 = <<<EOD
<?php
/**
 * @tag bar_{$rnd}
 */
class Bar{$rnd}Tag extends lmbMacroTag{}
EOD;

        file_put_contents($file1 = lmbEnv::get('LIMB_VAR_DIR') . '/tpl/tags/foo' . $rnd . 'Tag.php', $content1);
        file_put_contents($file2 = lmbEnv::get('LIMB_VAR_DIR') . '/tpl/tags/subfolder/bar' . $rnd . 'Tag.php', $content2);

        $tag_info1 = new lmbMacroTagInfo("foo_$rnd", "Foo{$rnd}Tag");
        $tag_info1->setFile($file1);
        $tag_info2 = new lmbMacroTagInfo("bar_$rnd", "Bar{$rnd}Tag");
        $tag_info2->setFile($file2);

        $config = $this->_createMacroConfig();
        $config->tags_scan_dirs = array(lmbEnv::get('LIMB_VAR_DIR') . '/tpl/tags/');
        $dictionary = new lmbMacroTagDictionary();
        $dictionary->load($config);

        $this->assertEquals($dictionary->findTagInfo("foo_$rnd")->getTag(), $tag_info1->getTag());
        $this->assertEquals($dictionary->findTagInfo("bar_$rnd")->getTag(), $tag_info2->getTag());
    }
}
