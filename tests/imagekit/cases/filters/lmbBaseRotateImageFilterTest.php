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

abstract class lmbBaseRotateImageFilterTest extends lmbImageKitTestCase
{
  function testRotate()
  {
    $cont = $this->_getContainer();
    $class_name = $this->_getFilterClass('lmb%RotateImageFilter');
    $filter = new $class_name(array('angle' => 90));

    $filter->apply($cont);
    $cont->save($this->_getOutputImage());
    list($width, $height, $type) = getimagesize($this->_getInputImage());
    list($width2, $height2, $type2) = getimagesize($this->_getOutputImage());
    $this->assertEquals($width, $height2);
    $this->assertEquals($height, $width2);
    $this->assertEquals($type, $type2);
  }

  function testParams()
  {
    $class_name = $this->_getFilterClass('lmb%RotateImageFilter');
    $filter = new $class_name(array('angle' => 90, 'bgcolor' => 'FF0000'));

    $this->assertEquals(90, $filter->getAngle());
    $bgcolor = $filter->getBgColor();
    $this->assertEquals(255, $bgcolor['red']);
    $this->assertEquals(0, $bgcolor['green']);
    $this->assertEquals(0, $bgcolor['blue']);
  }
}
