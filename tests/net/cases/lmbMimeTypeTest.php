<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\net\cases;

use PHPUnit\Framework\TestCase;
use limb\net\src\lmbMimeType;

class lmbMimeTypeTest extends TestCase
{
    function getExtensionFailed()
    {
        $this->assertNull(lmbMimeType::getExtension('foo'));
    }

    function testGetExtension()
    {
        $this->assertEquals('html', lmbMimeType::getExtension('text/html'));
        $this->assertEquals('rtf', lmbMimeType::getExtension('text/rtf'));
    }

    function testGetMimeTypeForExtensionFailed()
    {
        $this->assertNull(lmbMimeType::getMimeType('booo'));
    }

    function testGetMimeTypeForExtension()
    {
        $this->assertEquals('text/html', lmbMimeType::getMimeType('html'));
        $this->assertEquals('text/rtf', lmbMimeType::getMimeType('rtf'));
    }

    function testGetMimeTypeExtensionWithDot()
    {
        $this->assertEquals('text/html', lmbMimeType::getMimeType('.html'));
        $this->assertEquals('text/rtf', lmbMimeType::getMimeType('.rtf'));
    }

    function testGetMimeTypeForFileFailed()
    {
        $this->assertNull(lmbMimeType::getFileMimeType('booo.fg'));
    }

    function testGetMimeTypeForFile()
    {
        $this->assertEquals('text/html', lmbMimeType::getFileMimeType('test.html'));
        $this->assertEquals('text/rtf', lmbMimeType::getFileMimeType('test.rtf'));
    }


    function testGetSameExtensionForDifferentMimeTypes()
    {
        $this->assertEquals('zip', lmbMimeType::getExtension('application/x-zip-compressed'));
        $this->assertEquals('zip', lmbMimeType::getExtension('application/zip'));
        $this->assertEquals('rar', lmbMimeType::getExtension('application/x-rar-compressed'));
        $this->assertEquals('rar', lmbMimeType::getExtension('application/rar'));
    }

}
