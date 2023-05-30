<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

namespace tests\filter_chain\cases;

require_once('.setup.php');

use limb\net\src\lmbHttpRequest;
use PHPUnit\Framework\TestCase;
use limb\filter_chain\src\lmbFilterChain;
use tests\filter_chain\cases\src\InterceptingFilterStub;
use tests\filter_chain\cases\src\OutputFilter1;
use tests\filter_chain\cases\src\OutputFilter2;

class lmbFilterChainTest extends TestCase
{
    var $fc;

    function setUp(): void
    {
        $this->request = new lmbHttpRequest();

        $this->fc = new lmbFilterChain();
    }

    function testProcess()
    {
        $mock_filter = new InterceptingFilterStub();

        $this->fc->registerFilter($mock_filter);

        $this->assertFalse($mock_filter->run);

        $response = $this->fc->process($this->request);

        $this->assertTrue($mock_filter->run);

        $this->assertInstanceOf(lmbFilterChain::class, $mock_filter->captured['filter_chain']);
    }

    function testProcessProperNesting()
    {
        $f1 = new OutputFilter1();
        $f2 = new OutputFilter2();
        $mock_filter = new InterceptingFilterStub();

        $this->fc->registerFilter($f1);
        $this->fc->registerFilter($f2);
        $this->fc->registerFilter($mock_filter);

        ob_start();

        $response = $this->fc->process($this->request);

        $str = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('<filter1><filter2></filter2></filter1>', $str);
    }

    function testFilterChainAsAFilter()
    {
        $f1 = new OutputFilter1();
        $f2 = new OutputFilter2();
        $mock_filter = new InterceptingFilterStub();

        $fc1 = new lmbFilterChain();
        $fc1->registerFilter($f1);
        $fc1->registerFilter($mock_filter);

        $fc2 = new lmbFilterChain();
        $fc2->registerFilter($f2);
        $fc2->registerFilter($mock_filter);

        $fc = new lmbFilterChain();
        $fc->registerFilter($fc1);
        $fc->registerFilter($fc2);
        $fc->registerFilter($mock_filter);

        ob_start();

        $response = $fc->process(
            request()
        );

        $str = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('<filter1></filter1><filter2></filter2>', $str);
    }
}
