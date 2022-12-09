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

class lmbFsDictionaryExtractorTest extends TestCase
{

  function setUp(): void
  {
    $this->markTestSkipped('TODO: replace cli by taskman');
  }  
	
  function testLoad()
  {
    $it = $this->createMock(lmbSourceDictionaryExtractor::class);
    $m1 = $this->createMock(lmbFsRecursiveIterator::class);
    $m2 = $this->createMock(lmbFsRecursiveIterator::class);

    $it->expects($this->at(0))
        ->method('valid')
        ->willReturn(true);
    $it->method('isFile')->setReturnValueAt(false);
    $it->method('current')->setReturnValueAt('junky');

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
    $m1
        ->expects($this->once())
        ->method('extractFromFile', array($file_path1, $dictionaries, $response));
    $m2
        ->expects($this->once())
        ->method('extractFromFile', array($file_path2, $dictionaries, $response));

    $loader->traverse($it, $dictionaries, $response);
  }
}
