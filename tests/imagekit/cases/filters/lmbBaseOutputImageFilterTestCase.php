<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\imagekit\cases\filters;

use tests\imagekit\cases\lmbImageKitTestCase;

abstract class lmbBaseOutputImageFilterTestCase extends lmbImageKitTestCase
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
