<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\imagekit\cases;

abstract class lmbBaseImageConvertorTestCase extends lmbImageKitTestCase
{
    function testApply()
    {
        $conv = $this->_getConvertor();
        $conv->load($this->_getInputImage());
        $conv->apply('resize', array('width' => 50, 'height' => 70, 'preserve_aspect_ratio' => false));

        $conv->save($this->_getOutputImage());
        list($width, $height, $type) = getimagesize($this->_getOutputImage());
        $this->assertEquals(50, $width);
        $this->assertEquals(70, $height);
    }

    function testApplyByOverload()
    {
        $conv = $this->_getConvertor();
        $conv->load($this->_getInputImage());
        $conv->resize(array('width' => 50, 'height' => 70, 'preserve_aspect_ratio' => false));

        $conv->save($this->_getOutputImage());
        list($width, $height, $type) = getimagesize($this->_getOutputImage());
        $this->assertEquals(50, $width);
        $this->assertEquals(70, $height);
    }

    function testApplyBatch()
    {
        $batch = array(
            array('resize' => array('width' => 50, 'height' => 60, 'preserve_aspect_ratio' => false)),
            array('crop' => array('width' => 30, 'height' => 40, 'x' => 0, 'y' => 0))
        );
        $conv = $this->_getConvertor();
        $conv->load($this->_getInputImage());
        $conv->applyBatch($batch);

        $conv->save($this->_getOutputImage());
        list($width, $height, $type) = getimagesize($this->_getOutputImage());
        $this->assertEquals(30, $width);
        $this->assertEquals(40, $height);
    }

//    function testFilterLocator()
//    {
//        $path = dirname(__FILE__) . '/../fixture/filters';
//        $conv = $this->_getConvertor(array('add_filters_scan_dirs' => $path));
//        $conv->load($this->_getInputImage());
//        $conv->apply('test');
//        $conv = $this->_getConvertor(array('add_filters_scan_dirs' => array($path)));
//        $conv->load($this->_getInputImage());
//        $conv->apply('test');
//        $conv = $this->_getConvertor(array('filters_scan_dirs' => $path));
//        $conv->load($this->_getInputImage());
//        $conv->apply('test');
//        $conv = $this->_getConvertor(array('filters_scan_dirs' => array($path)));
//        $conv->load($this->_getInputImage());
//        $conv->apply('test');
//    }

    function testCheckSupportConv()
    {
        $conv = $this->_getConvertor();

        $this->assertTrue($conv->isSupportConversion('', 'jpeg', 'gif'));
        $this->assertTrue($conv->isSupportConversion($this->_getInputImage()));
        $this->assertFalse($conv->isSupportConversion($this->_getInputImage(), '', 'zxzx'));
    }

}
