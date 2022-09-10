<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

use PHPUnit\Framework\TestCase;
use limb\net\src\lmbMimeType;

class lmbMimeTypeTest extends TestCase
{
  function getExtensionFailed()
  {
    $this->assertNull(lmbMimeType :: getExtension('foo'));
  }

  function testGetExtension()
  {
    $this->assertEquals(lmbMimeType :: getExtension('text/html'), 'html');
    $this->assertEquals(lmbMimeType :: getExtension('text/rtf'), 'rtf');
  }

  function testGetMimeTypeForExtensionFailed()
  {
    $this->assertNull(lmbMimeType :: getMimeType('booo'));
  }

  function testGetMimeTypeForExtension()
  {
    $this->assertEquals(lmbMimeType :: getMimeType('html'), 'text/html');
    $this->assertEquals(lmbMimeType :: getMimeType('rtf'), 'text/rtf');
  }

  function testGetMimeTypeExtensionWithDot()
  {
    $this->assertEquals(lmbMimeType :: getMimeType('.html'), 'text/html');
    $this->assertEquals(lmbMimeType :: getMimeType('.rtf'), 'text/rtf');
  }

  function testGetMimeTypeForFileFailed()
  {
    $this->assertNull(lmbMimeType :: getFileMimeType('booo.fg'));
  }

  function testGetMimeTypeForFile()
  {
    $this->assertEquals(lmbMimeType :: getFileMimeType('test.html'), 'text/html');
    $this->assertEquals(lmbMimeType :: getFileMimeType('test.rtf'), 'text/rtf');
  }


  function testGetSameExtensionForDifferentMimeTypes()
  {
    $this->assertEquals(lmbMimeType :: getExtension('application/x-zip-compressed'), 'zip');
    $this->assertEquals(lmbMimeType :: getExtension('application/zip'), 'zip');
    $this->assertEquals(lmbMimeType :: getExtension('application/x-rar-compressed'), 'rar');
    $this->assertEquals(lmbMimeType :: getExtension('application/rar'), 'rar');
  }

}

