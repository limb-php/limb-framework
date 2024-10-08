<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\i18n\cases\translation;

use PHPUnit\Framework\TestCase;
use limb\core\lmbEnv;
use limb\i18n\translation\lmbI18NDictionary;
use limb\i18n\translation\lmbQtDictionaryBackend;
use limb\fs\lmbFs;

class lmbQtDictionaryBackendTest extends TestCase
{
    function setUp(): void
    {
        lmbFs::mkdir(lmbEnv::get('LIMB_VAR_DIR'));
    }

    function tearDown(): void
    {
        lmbFs::rm(lmbEnv::get('LIMB_VAR_DIR'));
    }

    function testLoadFromXML()
    {
        $back = new lmbQtDictionaryBackend();

        $xml = <<< EOD
<?xml version="1.0"?>
<!DOCTYPE TS><TS>
<context>
<message>
    <source>Hello</source>
    <translation>Привет</translation>
</message>
<message>
    <source>Hi</source>
    <translation>Привет</translation>
</message>
<message>
    <source>Dog</source>
    <translation>Собака</translation>
</message>
</context>
</TS>
EOD;

        $d = $back->loadFromXML($xml);

        $this->assertEquals($d->translate('Hello'), 'Привет');
        $this->assertEquals($d->translate('Hi'), 'Привет');
        $this->assertEquals($d->translate('Dog'), 'Собака');
    }

    function testLoadFromFile()
    {
        $back = new lmbQtDictionaryBackend();

        $xml = <<< EOD
<?xml version="1.0"?>
<!DOCTYPE TS><TS>
<context>
<message>
    <source>Hello</source>
    <translation>Привет</translation>
</message>
<message>
    <source>Hi</source>
    <translation>Привет</translation>
</message>
<message>
    <source>Dog</source>
    <translation>Собака</translation>
</message>
</context>
</TS>
EOD;

        file_put_contents($file = lmbEnv::get('LIMB_VAR_DIR') . '/dictionary.xml', $xml);

        $d = $back->loadFromFile($file);

        $this->assertEquals($d->translate('Hello'), 'Привет');
        $this->assertEquals($d->translate('Hi'), 'Привет');
        $this->assertEquals($d->translate('Dog'), 'Собака');
    }

    function testLoadSave()
    {
        $back = new lmbQtDictionaryBackend();
        $back->setSearchPath(lmbEnv::get('LIMB_VAR_DIR') . '/translations');

        $xml = <<< EOD
<?xml version="1.0"?>
<!DOCTYPE TS><TS>
<context>
<message>
    <source>Hello</source>
    <translation>Привет</translation>
</message>
<message>
    <source>Hi</source>
    <translation>Привет</translation>
</message>
<message>
    <source>Dog</source>
    <translation>Собака</translation>
</message>
</context>
</TS>
EOD;

        lmbFs::mkdir(lmbEnv::get('LIMB_VAR_DIR') . '/translations/');
        file_put_contents($file = lmbEnv::get('LIMB_VAR_DIR') . '/translations/default.ru_RU.ts', $xml);

        $d1 = $back->load('ru_RU', 'default');

        $this->assertEquals($d1->translate('Hello'), 'Привет');
        $this->assertEquals($d1->translate('Hi'), 'Привет');
        $this->assertEquals($d1->translate('Dog'), 'Собака');

        $d1->add('Hello', 'Превед');
        $back->save('ru_RU', 'default', $d1);

        $d2 = $back->load('ru_RU', 'default');

        $this->assertEquals($d2->translate('Hello'), 'Превед');
        $this->assertEquals($d2->translate('Hi'), 'Привет');
        $this->assertEquals($d2->translate('Dog'), 'Собака');
    }

    function testLoadAll()
    {
        $back = new lmbQtDictionaryBackend();
        $back->setSearchPath(lmbEnv::get('LIMB_VAR_DIR') . '/translations');

        $xml = <<< EOD
<?xml version="1.0"?>
<!DOCTYPE TS><TS>
<context>
<message>
    <source>Hello</source>
    <translation>Привет</translation>
</message>
</context>
</TS>
EOD;

        $d = $back->loadFromXML($xml);

        lmbFs:: mkdir(lmbEnv::get('LIMB_VAR_DIR') . '/translations/');
        file_put_contents($file1 = lmbEnv::get('LIMB_VAR_DIR') . '/translations/default.ru_RU.ts', $xml);
        file_put_contents($file2 = lmbEnv::get('LIMB_VAR_DIR') . '/translations/default.en_US.ts', $xml);

        $dicts = $back->loadAll();
        $this->assertTrue($dicts['ru_RU']['default']->isEqual($d));
        $this->assertTrue($dicts['en_US']['default']->isEqual($d));
    }

    function testUnfinishedTranslations()
    {
        $d = new lmbI18NDictionary();
        $back = new lmbQtDictionaryBackend();

        $d->add('Foo');
        $d->add('Bar', 'Бар');

        $dom = $back->getDOMDocument($d);
        $translations = $dom->getElementsByTagName('translation');

        $this->assertEquals($translations->item(0)->getAttribute('type'), 'unfinished');
        $this->assertFalse($translations->item(1)->hasAttribute('type'));
    }
}
