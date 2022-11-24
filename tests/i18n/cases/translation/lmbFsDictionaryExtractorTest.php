<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\i18n\cases\translation;

/**
 * TODO replace cli by taskman
 */
return;

use PHPUnit\Framework\TestCase;
use limb\cli\src\lmbCliResponse;
use limb\fs\src\lmbFsRecursiveIterator;
use limb\i18n\src\translation\lmbSourceDictionaryExtractor;
use limb\i18n\src\translation\lmbFsDictionaryExtractor;

Mock :: generate('lmbSourceDictionaryExtractor', 'MockBaseDictionaryParser');
Mock :: generate('lmbFsRecursiveIterator', 'MockFsRecursiveIterator');

class lmbFsDictionaryExtractorTest extends TestCase
{
	
	function skip()
  {
    $this->skipIf(true, 'TODO: replace cli by taskman');
  }  
	
  function testLoad()
  {
    $it = new MockFsRecursiveIterator();
    $m1 = new MockBaseDictionaryParser();
    $m2 = new MockBaseDictionaryParser();

    $it->setReturnValueAt(0, 'valid', true);
    $it->setReturnValueAt(0, 'isFile', false);
    $it->setReturnValueAt(0, 'current', 'junky');

    $file_path1 = 'some.php';
    $file_path2 = 'some.html';

    $it->setReturnValueAt(1, 'valid', true);
    $it->setReturnValueAt(1, 'current', $file_path1);
    $it->setReturnValueAt(1, 'isFile', true);

    $it->setReturnValueAt(2, 'valid', true);
    $it->setReturnValueAt(2, 'current', $file_path2);
    $it->setReturnValueAt(2, 'isFile', true);

    $loader = new lmbFsDictionaryExtractor();
    $loader->registerFileParser('.php', $m1);
    $loader->registerFileParser('.html', $m2);

    $dictionaries = array();

    $response = new lmbCliResponse();
    $m1->expectOnce('extractFromFile', array($file_path1, $dictionaries, $response));
    $m2->expectOnce('extractFromFile', array($file_path2, $dictionaries, $response));

    $loader->traverse($it, $dictionaries, $response);
  }
}
