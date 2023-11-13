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

abstract class lmbBaseOutputImageFilterTest extends lmbImageKitTestCase
{
  function testChangeOutput()
  {
    $cont = $this->_getContainer();
    $cont->setOutputType('gif');

    $class_name = $this->_getFilterClass('lmb%OutputImageFilter');
    $filter = new $class_name(array('type' => 'jpeg'));
    $filter->apply($cont);

    $this->assertEquals('jpeg', $cont->getOutputType());
  }
}
