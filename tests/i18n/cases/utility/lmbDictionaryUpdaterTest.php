<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\i18n\cases\utility;

/**
 * TODO replace cli by taskman
 */

use limb\core\src\lmbEnv;
use PHPUnit\Framework\TestCase;
use limb\cli\src\lmbCliResponse;
use limb\fs\src\lmbFs;
use limb\i18n\src\translation\lmbQtDictionaryBackend;
use limb\i18n\src\translation\lmbDictionaryUpdater;

class lmbDictionaryUpdaterTest extends TestCase
{	

  function setUp(): void
  {
      $this->markTestSkipped('TODO: replace cli by taskman');

      $this->_cleanUp();

      lmbFs::mkdir(lmbEnv::get('LIMB_VAR_DIR'). '/translations');
      lmbFs::mkdir(lmbEnv::get('LIMB_VAR_DIR'). '/src');
      lmbFs::mkdir(lmbEnv::get('LIMB_VAR_DIR'). '/parse1');
      lmbFs::mkdir(lmbEnv::get('LIMB_VAR_DIR'). '/parse2');
  }

  function tearDown(): void
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    lmbFs::rm(lmbEnv::get('LIMB_VAR_DIR') . '/translations');
    lmbFs::rm(lmbEnv::get('LIMB_VAR_DIR') . '/src');
  }

  function testUpdateTranslations()
  {
    $translations_dir = lmbEnv::get('LIMB_VAR_DIR') . '/translations';
    $ru_file = $translations_dir . '/foo.ru_RU.ts';
    $de_file = $translations_dir . '/foo.de_DE.ts';

    $source_dir = lmbEnv::get('LIMB_VAR_DIR') . '/src/';
    $html_file = $source_dir . '/hourse.html';
    $php_file = $source_dir . '/cat.php';

    $xml = <<< EOD
<?xml version="1.0"?>
<!DOCTYPE TS><TS>
<context>
<message>
    <source>Dog</source>
    <translation>Dog</translation>
</message>
</context>
</TS>
EOD;
    file_put_contents($ru_file, $xml);
    file_put_contents($de_file, $xml);

    $php = <<< EOD
<?php
lmb_i18n('Cat', 'foo');
?>
EOD;
    file_put_contents($php_file, $php);

    $html = <<< EOD
{\$'Horse'|i18n:'foo'}
EOD;
    file_put_contents($html_file, $html);

    $cli_responce = $this->createMock(lmbCliResponse::class);
    $backend = new lmbQtDictionaryBackend();
    $backend->setSearchPath($translations_dir);

    $updater = new lmbDictionaryUpdater($backend, $cli_responce);
    $updater->updateTranslations($source_dir);

    $ru_dictionary = $backend->loadFromFile($ru_file);
    $this->assertTrue($ru_dictionary->has('Horse'));
    $this->assertTrue($ru_dictionary->has('Cat'));
    $this->assertTrue($ru_dictionary->has('Dog'));

    $de_dictionary = $backend->loadFromFile($de_file);
    $this->assertTrue($de_dictionary->has('Horse'));
    $this->assertTrue($de_dictionary->has('Cat'));
    $this->assertTrue($de_dictionary->has('Dog'));
  }

  function testUpdateTranslationsForDefaultContext()
  {
    $translations_dir = lmbEnv::get('LIMB_VAR_DIR') . '/translations';
    $ru_file = $translations_dir . '/default.ru_RU.ts';
    $de_file = $translations_dir . '/default.de_DE.ts';

    $source_dir = lmbEnv::get('LIMB_VAR_DIR') . '/src/';
    $html_file = $source_dir . '/hourse.html';
    $php_file = $source_dir . '/cat.php';

    $xml = <<< EOD
<?xml version="1.0"?>
<!DOCTYPE TS><TS>
<context>
<message>
    <source>Dog</source>
    <translation>Dog</translation>
</message>
</context>
</TS>
EOD;
    file_put_contents($ru_file, $xml);
    file_put_contents($de_file, $xml);

    $php = <<< EOD
<?php
lmb_i18n('Cat');
?>
EOD;
    file_put_contents($php_file, $php);

    $html = <<< EOD
{\$'Horse'|i18n}
EOD;
    file_put_contents($html_file, $html);

    $cli_responce = $this->createMock(lmbCliResponse::class);
    $backend = new lmbQtDictionaryBackend();
    $backend->setSearchPath($translations_dir);

    $updater = new lmbDictionaryUpdater($backend, $cli_responce);
    $updater->updateTranslations($source_dir);

    $ru_dictionary = $backend->loadFromFile($ru_file);
    $this->assertTrue($ru_dictionary->has('Horse'));
    $this->assertTrue($ru_dictionary->has('Cat'));
    $this->assertTrue($ru_dictionary->has('Dog'));

    $de_dictionary = $backend->loadFromFile($de_file);
    $this->assertTrue($de_dictionary->has('Horse'));
    $this->assertTrue($de_dictionary->has('Cat'));
    $this->assertTrue($de_dictionary->has('Dog'));
  }
}
