<?php
/*
* Limb PHP Framework
*
* @link http://limb-project.com
* @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
* @license    LGPL http://www.gnu.org/copyleft/lesser.html
*/
namespace Tests\view\cases;

require_once '.setup.php';

use PHPUnit\Framework\TestCase;
use limb\view\src\lmbBlitzView;
use limb\core\src\lmbEnv;

class lmbBlitzViewTest extends TestCase
{
    function setUp(): void
    {
        if(!extension_loaded('blitz'))
            $this->markTestSkipped('Blitz extension not found. Test skipped.');

        if(!class_exists('Blitz'))
            $this->markTestSkipped('Blitz class not found. Test skipped.');
    }

    private function _createTemplateFile($name, $source)
    {
        file_put_contents($path = lmbEnv::get('LIMB_VAR_DIR') . $name, $source);
        return $path;
    }

    function testRenderSimpleVars()
    {
        $template = '{{$hello}}{{$again}}';
        $path = $this->_createTemplateFile('/simple.bhtml', $template);

        $view = new lmbBlitzView($path);
        $view->set('hello', 'Hello message!');
        $view->set('again', 'Hello again!');

        $this->assertEquals($view->render(), 'Hello message!Hello again!');
    }

    function testManualTemplateFunctionCall()
    {
        $template = '{{BEGIN foo}}{{END}}';
        $path = $this->_createTemplateFile('/simple.bhtml', $template);

        $view = new lmbBlitzView($path);
        $this->assertTrue($view->hasContext('foo'));
        $this->assertFalse($view->hasContext('bar'));
    }

    function testRenderIteratedTemplates()
    {
        $template =
            '{{ BEGIN outer }}o'
                .'{{ $ovar }}'
                .'{{ BEGIN inner }}i'
                    .'{{ $ivar }}'
                .'{{ END inner }}'
            .'{{ END }}';

        $data = array (
                array(
                    'ovar' => 'a',
                    'inner' => array(
                        array('ivar' => '1'),
                        array('ivar' => '2'),
                        array('ivar' => '3'),
                        ),
                ),
                array(
                    'ovar' => 'b',
                    'inner' => array(
                        array('ivar' => '4'),
                        array('ivar' => '5'),
                        array('ivar' => '6'),
                        ),
                )
        );

        $out = 'oai1i2i3obi4i5i6';

        $path = $this->_createTemplateFile('/iteration.bhtml', $template);

        $view = new lmbBlitzView($path);
        $view->set('outer', $data);

        $this->assertEquals($view->render(), $out);
    }

}
