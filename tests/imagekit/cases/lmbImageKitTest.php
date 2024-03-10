<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\imagekit\cases;

use PHPUnit\Framework\TestCase;
use limb\imagekit\src\lmbImageKit;
use limb\imagekit\src\gd\lmbGdImageConvertor;

require_once(dirname(__FILE__) . '/.setup.php');

class lmbImageKitTest extends TestCase
{
    function setUp(): void
    {
        parent::setUp();

        if (!extension_loaded('gd'))
            $this->markTestSkipped('GD extension not found. Test skipped.');

        if (!function_exists('imagerotate'))
            $this->markTestSkipped('imagerotate() function does not exist. Test skipped.');
    }

    function tearDown(): void
    {
        @unlink($this->_getOutputImage());

        parent::tearDown();
    }

    function _getInputImage()
    {
        return dirname(__FILE__) . '/../fixture/images/input.jpg';
    }

    function _getOutputImage()
    {
        return lmb_var_dir() . '/output.jpg';
    }

    function testCreateGdConvertor()
    {
        $conv = lmbImageKit::create('gd');
        $this->assertInstanceOf(lmbGdImageConvertor::class, $conv);
    }

    function testTraversing()
    {
        lmbImageKit::load($this->_getInputImage())->
        apply('resize', array('width' => 50, 'height' => 60, 'preserve_aspect_ratio' => false))->
        apply('rotate', array('angle' => 90))->
        save($this->_getOutputImage());

        list($width, $height, $type) = getimagesize($this->_getOutputImage());
        $this->assertEquals(60, $width);
        $this->assertEquals(50, $height);
    }

    function testTraversingByOverloading()
    {
        lmbImageKit::load($this->_getInputImage())->
        resize(array('width' => 50, 'height' => 60, 'preserve_aspect_ratio' => false))->
        rotate(array('angle' => 90))->
        save($this->_getOutputImage());

        list($width, $height, $type) = getimagesize($this->_getOutputImage());
        $this->assertEquals(60, $width);
        $this->assertEquals(50, $height);
    }

//    function testPassingParamsToConvertor()
//    {
//        lmbImageKit::load($this->_getInputImage(), '', 'gd', array('add_filters_scan_dirs' => dirname(__FILE__) . '/../fixture/filters'))
//            ->test();
//    }
}
