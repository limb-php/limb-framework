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
use limb\i18n\src\translation\lmbQtDictionaryBackend;
use limb\fs\src\lmbFs;
use limb\toolkit\src\lmbToolkit;

class lmbI18NTranslationTest extends TestCase
{
  function setUp(): void
  {
    lmbFs :: mkdir(LIMB_VAR_DIR . '/translations');
  }

  function tearDown(): void
  {
    lmbFs :: rm(LIMB_VAR_DIR . '/translations');
  }

  function testTranslate()
  {
    $toolkit = lmbToolkit :: save();
    $back = new lmbQtDictionaryBackend();
    $back->setSearchPath($translations_dir = LIMB_VAR_DIR . '/translations');
    $toolkit->setDictionaryBackend($back);

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
    file_put_contents($translations_dir . '/foo.ru_RU.ts', $xml);

    $toolkit->setLocale('ru_RU');
    $this->assertEquals(lmb_i18n('Hello', 'foo'), 'Привет');

    lmbToolkit :: restore();
  }

  function testTranslateDefaultContext()
  {
    $toolkit = lmbToolkit :: save();
    $back = new lmbQtDictionaryBackend();
    $back->setSearchPath($translations_dir = LIMB_VAR_DIR . '/translations');
    $toolkit->setDictionaryBackend($back);

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
    file_put_contents($translations_dir . '/default.ru_RU.ts', $xml);

    $toolkit->setLocale('ru_RU');
    $this->assertEquals(lmb_i18n('Hello'), 'Привет');

    lmbToolkit :: restore();
  }

  function testTranslateSubstituteParameters()
  {
    $toolkit = lmbToolkit :: save();
    $back = new lmbQtDictionaryBackend();
    $back->setSearchPath($translations_dir = LIMB_VAR_DIR . '/translations');
    $toolkit->setDictionaryBackend($back);

    $xml = <<< EOD
<?xml version="1.0"?>
<!DOCTYPE TS><TS>
<context>
<message>
    <source>Hello {name}</source>
    <translation>Привет {name}</translation>
</message>
</context>
</TS>
EOD;
    file_put_contents($translations_dir . '/foo.ru_RU.ts', $xml);

    $toolkit->setLocale('ru_RU');
    $this->assertEquals(lmb_i18n('Hello {name}', array('{name}' => 'Bob'), 'foo'), 'Привет Bob');

    lmbToolkit :: restore();
  }

  function testTranslateSubstituteParametersDefaultContext()
  {
    $toolkit = lmbToolkit :: save();
    $back = new lmbQtDictionaryBackend();
    $back->setSearchPath($translations_dir = LIMB_VAR_DIR . '/translations');
    $toolkit->setDictionaryBackend($back);

    $xml = <<< EOD
<?xml version="1.0"?>
<!DOCTYPE TS><TS>
<context>
<message>
    <source>Hello {name}</source>
    <translation>Привет {name}</translation>
</message>
</context>
</TS>
EOD;
    file_put_contents($translations_dir . '/default.ru_RU.ts', $xml);

    $toolkit->setLocale('ru_RU');
    $this->assertEquals(lmb_i18n('Hello {name}', array('{name}' => 'Bob')), 'Привет Bob');

    lmbToolkit :: restore();
  }
}
