<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\imagekit\cases\filters;

use tests\imagekit\cases\lmbImageKitTestCase;

/**
 * @package imagekit
 * @version $Id: lmbGdCropImageFilterTest.php 7486 2009-01-26 19:13:20Z pachanga $
 */
abstract class lmbBaseCropImageFilterTest extends lmbImageKitTestCase
{
  function testTrueColorCrop()
  {
    $cont = $this->_getContainer();
    $class_name = $this->_getClass('lmb%CropImageFilter');
    $filter = new $class_name(array('width' => 50, 'height' => 70, 'x' => 10, 'y' => 20));

    $filter->apply($cont);
    $cont->save($this->_getOutputImage());
    list($width, $height, $type) = getimagesize($this->_getOutputImage());
    $this->assertEquals($width, 50);
    $this->assertEquals($height, 70);
    $cont->load($this->_getOutputImage());
    $this->assertFalse($cont->isPallete());
  }

  function testPalleteCrop()
  {
    $cont = $this->_getPalleteContainer();
    $class_name = $this->_getClass('lmb%CropImageFilter');
    $filter = new $class_name(array('width' => 5, 'height' => 10));

    $filter->apply($cont);
    $cont->save($this->_getOutputImage());
    list($width, $height, $type) = getimagesize($this->_getOutputImage());
    $this->assertEquals($width, 5);
    $this->assertEquals($height, 10);
    $cont->load($this->_getOutputImage());
    $this->assertTrue($cont->isPallete());
  }

  function testMaxSizeRestriction()
  {
    $cont = $this->_getPalleteContainer();
    $class_name = $this->_getClass('lmb%CropImageFilter');
    $filter = new $class_name(array('width' => 100, 'height' => 100));

    $filter->apply($cont);
    $cont->save($this->_getOutputImage());
    list($width, $height, $type) = getimagesize($this->_getOutputImage());
    $this->assertEquals($width, 14);
    $this->assertEquals($height, 15);
    $cont->load($this->_getOutputImage());
  }

  function testCropOfInternalArea()
  {
    $cont = $this->_getContainer();
    $class_name = $this->_getClass('lmb%CropImageFilter');
    $filter = new $class_name(array('x' => 10, 'y' => 20, 'width' => 40, 'height' => 50));

    $filter->apply($cont);
    $cont->save($this->_getOutputImage());
    list($width, $height, $type) = getimagesize($this->_getOutputImage());
    $this->assertEquals($width, 40);
    $this->assertEquals($height, 50);
    $cont->load($this->_getOutputImage());
  }

  function testAutoDetectionOfSize()
  {
    $cont = $this->_getContainer();
    $class_name = $this->_getClass('lmb%CropImageFilter');
    $filter = new $class_name(array('x' => 10, 'y' => 20));

    $filter->apply($cont);
    $cont->save($this->_getOutputImage());
    list($width, $height, $type) = getimagesize($this->_getOutputImage());
    $this->assertEquals($width, 90);
    $this->assertEquals($height, 117);
    $cont->load($this->_getOutputImage());
  }

  function testParams()
  {
    $class_name = $this->_getClass('lmb%CropImageFilter');
    $filter = new $class_name(array('width' => 90, 'height' => 100, 'x' => 10, 'y' => 20));

    $this->assertEquals($filter->getWidth(), 90);
    $this->assertEquals($filter->getHeight(), 100);
    $this->assertEquals($filter->getX(), 10);
    $this->assertEquals($filter->getY(), 20);
  }
}
