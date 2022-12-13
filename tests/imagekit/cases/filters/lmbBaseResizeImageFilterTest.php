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

abstract class lmbBaseResizeImageFilterTest extends lmbImageKitTestCase
{
  function testSimpleResize()
  {
    $cont = $this->_getContainer();
    $class_name = $this->_getFilterClass('lmb%ResizeImageFilter');
    $filter = new $class_name(array('width' => 50, 'height' => 70, 'preserve_aspect_ratio' => false));

    $filter->apply($cont);
    $cont->save($this->_getOutputImage());
    list($width, $height, $type) = getimagesize($this->_getOutputImage());
    $this->assertEquals($width, 50);
    $this->assertEquals($height, 70);
  }

  function testPreserveAspectRatio()
  {
    $class_name = $this->_getFilterClass('lmb%ResizeImageFilter');
    $filter = new $class_name(array());

    list($w, $h) = $filter->calcSize(100, 60, 20, 30, true);
    $this->assertEquals($w, 20);
    $this->assertEquals($h, 12);

    list($w, $h) = $filter->calcSize(60, 100, 20, 30, true);
    $this->assertEquals($w, 18);
    $this->assertEquals($h, 30);
  }

  function testSaveMinSize()
  {
    $class_name = $this->_getFilterClass('lmb%ResizeImageFilter');
    $filter = new $class_name(array());

    list($w, $h) = $filter->calcSize(100, 60, 20, 30, true, true);
    $this->assertEquals($w, 20);
    $this->assertEquals($h, 12);

    list($w, $h) = $filter->calcSize(60, 100, 20, 30, true, true);
    $this->assertEquals($w, 18);
    $this->assertEquals($h, 30);

    list($w, $h) = $filter->calcSize(10, 20, 20, 30, true, true);
    $this->assertEquals($w, 10);
    $this->assertEquals($h, 20);

    list($w, $h) = $filter->calcSize(10, 20, 20, 30, true);
    $this->assertEquals($w, 15);
    $this->assertEquals($h, 30);

    list($w, $h) = $filter->calcSize(10, 20, 20, 30, false, true);
    $this->assertEquals($w, 10);
    $this->assertEquals($h, 20);
  }

  function testParams()
  {
    $class_name = $this->_getFilterClass('lmb%ResizeImageFilter');
    $filter = new $class_name(array('width' => 90, 'height' => 100, 'preserve_aspect_ratio' => false, 'save_min_size' => true));

    $this->assertEquals($filter->getWidth(), 90);
    $this->assertEquals($filter->getHeight(), 100);
    $this->assertFalse($filter->getPreserveAspectRatio());
    $this->assertTrue($filter->getSaveMinSize());
  }
}
