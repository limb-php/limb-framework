<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace Tests\imagekit\cases\filters;

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
    $this->assertEquals(50, $width);
    $this->assertEquals(70, $height);
  }

  function testPreserveAspectRatio()
  {
    $class_name = $this->_getFilterClass('lmb%ResizeImageFilter');
    $filter = new $class_name(array());

    list($w, $h) = $filter->calcSize(100, 60, 20, 30, true);
    $this->assertEquals(20, $w);
    $this->assertEquals(12, $h);

    list($w, $h) = $filter->calcSize(60, 100, 20, 30, true);
    $this->assertEquals(18, $w);
    $this->assertEquals(30, $h);
  }

  function testSaveMinSize()
  {
    $class_name = $this->_getFilterClass('lmb%ResizeImageFilter');
    $filter = new $class_name(array());

    list($w, $h) = $filter->calcSize(100, 60, 20, 30, true, true);
    $this->assertEquals(20, $w);
    $this->assertEquals(12, $h);

    list($w, $h) = $filter->calcSize(60, 100, 20, 30, true, true);
    $this->assertEquals(18, $w);
    $this->assertEquals(30, $h);

    list($w, $h) = $filter->calcSize(10, 20, 20, 30, true, true);
    $this->assertEquals(10, $w);
    $this->assertEquals(20, $h);

    list($w, $h) = $filter->calcSize(10, 20, 20, 30, true);
    $this->assertEquals(15, $w);
    $this->assertEquals(30, $h);

    list($w, $h) = $filter->calcSize(10, 20, 20, 30, false, true);
    $this->assertEquals(10, $w);
    $this->assertEquals(20, $h);
  }

  function testParams()
  {
    $class_name = $this->_getFilterClass('lmb%ResizeImageFilter');
    $filter = new $class_name(array('width' => 90, 'height' => 100, 'preserve_aspect_ratio' => false, 'save_min_size' => true));

    $this->assertEquals(90, $filter->getWidth());
    $this->assertEquals(100, $filter->getHeight());
    $this->assertFalse($filter->getPreserveAspectRatio());
    $this->assertTrue($filter->getSaveMinSize());
  }
}
