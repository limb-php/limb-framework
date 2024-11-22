<?php
/*
* Limb PHP Framework
*
* @license    LGPL http://www.gnu.org/copyleft/lesser.html
*/

namespace tests\view\cases;

require_once (dirname(__FILE__) . '/init.inc.php');

use PHPUnit\Framework\TestCase;
use limb\view\src\lmbJsonView;
use limb\core\src\lmbObject;

class lmbJsonViewTest extends TestCase
{
    /**
     * @var lmbJsonView
     */
    protected $view;

    function setUp(): void
    {
        $this->view = new lmbJsonView();
    }

    protected function _checkValue($must_be)
    {
        $this->assertEquals($this->view->render(), $must_be);
        $this->view->useEmulation(true);
        $this->assertEquals($this->view->render(), $must_be);
    }

    function testRender_empty()
    {
        $this->_checkValue('[]');
    }

    function testRender_bool()
    {
        $this->view
            ->with('positive', true)
            ->with('negative', false);

        $this->_checkValue('{"positive":true,"negative":false}');
    }

    function testRender_integer()
    {
        $this->view->set('integer', 42);
        $this->_checkValue('{"integer":42}');
    }

    function testRender_string()
    {
        $this->view->set('string', 'foo');
        $this->_checkValue('{"string":"foo"}');
    }

    function testRender_array()
    {
        $this->view->set('array', array(array(array('foo' => 42))));
        $this->_checkValue('{"array":[[{"foo":42}]]}');
    }

    function testRender_object()
    {
        $object = new lmbObject();
        $object->set('foo', 42);

        $this->view->set('object', $object);
        $this->_checkValue('{"object":{"foo":42}}');
    }
}
