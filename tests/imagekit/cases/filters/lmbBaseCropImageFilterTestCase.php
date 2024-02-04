<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\imagekit\cases\filters;

use Tests\imagekit\cases\lmbImageKitTestCase;

/**
 * @package imagekit
 * @version $Id: lmbGdCropImageFilterTest.php 7486 2009-01-26 19:13:20Z
 */
abstract class lmbBaseCropImageFilterTestCase extends lmbImageKitTestCase
{
    function testTrueColorCrop()
    {
        $cont = $this->_getContainer();
        $class_name = $this->_getFilterClass('lmb%CropImageFilter');
        $filter = new $class_name(array('width' => 50, 'height' => 70, 'x' => 10, 'y' => 20));

        $filter->apply($cont);
        $cont->save($this->_getOutputImage());
        list($width, $height, $type) = getimagesize($this->_getOutputImage());
        $this->assertEquals(50, $width);
        $this->assertEquals(70, $height);
        $cont->load($this->_getOutputImage());
        $this->assertFalse($cont->isPallete());
    }

    function testPalleteCrop()
    {
        $cont = $this->_getPalleteContainer();
        $class_name = $this->_getFilterClass('lmb%CropImageFilter');
        $filter = new $class_name(array('width' => 5, 'height' => 10));

        $filter->apply($cont);
        $cont->save($this->_getOutputImage());
        list($width, $height, $type) = getimagesize($this->_getOutputImage());
        $this->assertEquals(5, $width);
        $this->assertEquals(10, $height);
        $cont->load($this->_getOutputImage());
        $this->assertTrue($cont->isPallete());
    }

    function testMaxSizeRestriction()
    {
        $cont = $this->_getPalleteContainer();
        $class_name = $this->_getFilterClass('lmb%CropImageFilter');
        $filter = new $class_name(array('width' => 100, 'height' => 100));

        $filter->apply($cont);
        $cont->save($this->_getOutputImage());
        list($width, $height, $type) = getimagesize($this->_getOutputImage());
        $this->assertEquals(14, $width);
        $this->assertEquals(15, $height);
        $cont->load($this->_getOutputImage());
    }

    function testCropOfInternalArea()
    {
        $cont = $this->_getContainer();
        $class_name = $this->_getFilterClass('lmb%CropImageFilter');
        $filter = new $class_name(array('x' => 10, 'y' => 20, 'width' => 40, 'height' => 50));

        $filter->apply($cont);
        $cont->save($this->_getOutputImage());
        list($width, $height, $type) = getimagesize($this->_getOutputImage());
        $this->assertEquals(40, $width);
        $this->assertEquals(50, $height);
        $cont->load($this->_getOutputImage());
    }

    function testAutoDetectionOfSize()
    {
        $cont = $this->_getContainer();
        $class_name = $this->_getFilterClass('lmb%CropImageFilter');
        $filter = new $class_name(array('x' => 10, 'y' => 20));

        $filter->apply($cont);
        $cont->save($this->_getOutputImage());
        list($width, $height, $type) = getimagesize($this->_getOutputImage());
        $this->assertEquals(90, $width);
        $this->assertEquals(117, $height);
        $cont->load($this->_getOutputImage());
    }

    function testParams()
    {
        $class_name = $this->_getFilterClass('lmb%CropImageFilter');
        $filter = new $class_name(array('width' => 90, 'height' => 100, 'x' => 10, 'y' => 20));

        $this->assertEquals(90, $filter->getWidth());
        $this->assertEquals(100, $filter->getHeight());
        $this->assertEquals(10, $filter->getX());
        $this->assertEquals(20, $filter->getY());
    }
}
