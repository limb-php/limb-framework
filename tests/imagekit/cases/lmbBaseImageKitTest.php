<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\imagekit\cases;

use limb\imagekit\src\lmbImageKit;
use tests\imagekit\cases\lmbImageKitTestCase;

abstract class lmbBaseImageKitTest extends lmbImageKitTestCase
{
  function testTraversing()
  {
    lmbImageKit::load($this->_getInputImage(), $this->_getInputImageType(), $this->driver)->
      apply('resize', array('width' => 50, 'height' => 60, 'preserve_aspect_ratio' => false))->
      apply('crop', array('width' => 30, 'height' => 40, 'x' => 0, 'y' => 0))->
      save($this->_getOutputImage());

    list($width, $height, $type) = getimagesize($this->_getOutputImage());
    $this->assertEquals($width, 30);
    $this->assertEquals($height, 40);
  }

  function testTraversingByOverloading()
  {
    lmbImageKit::load($this->_getInputImage(), $this->_getInputImageType(), $this->driver)->
      resize(array('width' => 50, 'height' => 60, 'preserve_aspect_ratio' => false))->
      crop(array('width' => 30, 'height' => 40, 'x' => 0, 'y' => 0))->
      save($this->_getOutputImage());

    list($width, $height, $type) = getimagesize($this->_getOutputImage());
    $this->assertEquals($width, 30);
    $this->assertEquals($height, 40);
  }

  function testPassingParamsToConvertor()
  {
    lmbImageKit::load($this->_getInputImage(),
                      '',
                      $this->driver,
                      '',
                      array('add_filters_scan_dirs' => dirname(__FILE__).'/../fixture/filters')
    )->test();
  }
}
