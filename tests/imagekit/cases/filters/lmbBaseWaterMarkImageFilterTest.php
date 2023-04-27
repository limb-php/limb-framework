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

abstract class lmbBaseWaterMarkImageFilterTest extends lmbImageKitTestCase
{
  function testWaterMark()
  {
    $cont = $this->_getContainer();
    $class_name = $this->_getFilterClass('lmb%WaterMarkImageFilter');
    $filter = new $class_name(array('water_mark' => $this->_getInputPalleteImage(), 'x' => 5, 'y' => 6));

    $filter->apply($cont);
    $cont->save($this->_getOutputImage());
    list($width, $height, $type) = getimagesize($this->_getInputImage());
    list($width2, $height2, $type2) = getimagesize($this->_getOutputImage());
    $this->assertEquals($width, $width2);
    $this->assertEquals($height, $height2);
    $this->assertEquals($type, $type2);
  }

  function testParams()
  {
    $class_name = $this->_getFilterClass('lmb%WaterMarkImageFilter');
    $filter = new $class_name(array('water_mark' => 'input.jpg', 'x' => 90, 'y' => 100, 'opacity' => 20, 'xcenter' => true, 'ycenter' => true));

    $this->assertEquals('input.jpg', $filter->getWaterMark());
    $this->assertEquals(90, $filter->getX());
    $this->assertEquals(100, $filter->getY());
    $this->assertEquals(20, $filter->getOpacity());
    if(method_exists($filter, 'getXCenter'))
      $this->assertTrue($filter->getXCenter());
    if(method_exists($filter, 'getYCenter'))
    $this->assertTrue($filter->getYCenter());
  }

  function testCalcPosition()
  {
    $class_name = $this->_getFilterClass('lmb%WaterMarkImageFilter');
    $filter = new $class_name(array());

    $result = $filter->calcPosition(10, 100, 150, 250, false, false);
    $this->assertEquals(10, $result[0]);
    $this->assertEquals(100, $result[1]);

    $result = $filter->calcPosition(-10, 100, 150, 250, false, false);
    $this->assertEquals(140, $result[0]);
    $this->assertEquals(100, $result[1]);

    $result = $filter->calcPosition(10, -100, 150, 250, false, false);
    $this->assertEquals(10, $result[0]);
    $this->assertEquals(150, $result[1]);

    $result = $filter->calcPosition(-10, -100, 150, 250, false, false);
    $this->assertEquals(140, $result[0]);
    $this->assertEquals(150, $result[1]);

    $result = $filter->calcPosition(0, 0, 150, 250, 10, false);
    $this->assertEquals(70, $result[0]);
    $this->assertEquals(0, $result[1]);

    $result = $filter->calcPosition(0, 0, 150, 250, false, 10);
    $this->assertEquals(0, $result[0]);
    $this->assertEquals(120, $result[1]);

    $result = $filter->calcPosition(-5, 5, 150, 250, 20, 10);
    $this->assertEquals(60, $result[0]);
    $this->assertEquals(125, $result[1]);
  }
}
