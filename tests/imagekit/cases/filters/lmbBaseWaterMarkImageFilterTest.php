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

    $this->assertEquals($filter->getWaterMark(), 'input.jpg');
    $this->assertEquals($filter->getX(), 90);
    $this->assertEquals($filter->getY(), 100);
    $this->assertEquals($filter->getOpacity(), 20);
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
    $this->assertEquals($result[0], 10);
    $this->assertEquals($result[1], 100);

    $result = $filter->calcPosition(-10, 100, 150, 250, false, false);
    $this->assertEquals($result[0], 140);
    $this->assertEquals($result[1], 100);

    $result = $filter->calcPosition(10, -100, 150, 250, false, false);
    $this->assertEquals($result[0], 10);
    $this->assertEquals($result[1], 150);

    $result = $filter->calcPosition(-10, -100, 150, 250, false, false);
    $this->assertEquals($result[0], 140);
    $this->assertEquals($result[1], 150);

    $result = $filter->calcPosition(0, 0, 150, 250, 10, false);
    $this->assertEquals($result[0], 70);
    $this->assertEquals($result[1], 0);

    $result = $filter->calcPosition(0, 0, 150, 250, false, 10);
    $this->assertEquals($result[0], 0);
    $this->assertEquals($result[1], 120);

    $result = $filter->calcPosition(-5, 5, 150, 250, 20, 10);
    $this->assertEquals($result[0], 60);
    $this->assertEquals($result[1], 125);
  }
}
