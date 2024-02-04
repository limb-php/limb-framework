<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\imagekit\cases;

use limb\imagekit\src\lmbImageKit;

abstract class lmbBaseImageKitTestCase extends lmbImageKitTestCase
{
    function testTraversing()
    {
        lmbImageKit::load($this->_getInputImage(), $this->_getInputImageType(), $this->driver)->
        apply('resize', array('width' => 50, 'height' => 60, 'preserve_aspect_ratio' => false))->
        apply('crop', array('width' => 30, 'height' => 40, 'x' => 0, 'y' => 0))->
        save($this->_getOutputImage());

        list($width, $height, $type) = getimagesize($this->_getOutputImage());
        $this->assertEquals(30, $width);
        $this->assertEquals(40, $height);
    }

    function testTraversingByOverloading()
    {
        lmbImageKit::load($this->_getInputImage(), $this->_getInputImageType(), $this->driver)->
        resize(array('width' => 50, 'height' => 60, 'preserve_aspect_ratio' => false))->
        crop(array('width' => 30, 'height' => 40, 'x' => 0, 'y' => 0))->
        save($this->_getOutputImage());

        list($width, $height, $type) = getimagesize($this->_getOutputImage());
        $this->assertEquals(30, $width);
        $this->assertEquals(40, $height);
    }

//    function testPassingParamsToConvertor()
//    {
//        lmbImageKit::load($this->_getInputImage(),
//            '',
//            $this->driver,
//            array('add_filters_scan_dirs' => dirname(__FILE__) . '/../fixture/filters')
//        )->test();
//    }
}
