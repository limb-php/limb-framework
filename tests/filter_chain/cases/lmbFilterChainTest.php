<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\filter_chain\cases;

require_once(dirname(__FILE__) . '/.setup.php');

use limb\net\src\lmbHttpRequest;
use PHPUnit\Framework\TestCase;
use limb\filter_chain\src\lmbFilterChain;
use tests\filter_chain\cases\src\InterceptingFilterStub;
use tests\filter_chain\cases\src\OutputFilter1;
use tests\filter_chain\cases\src\OutputFilter2;
use tests\filter_chain\cases\src\OutputFilter3;

class lmbFilterChainTest extends TestCase
{
    protected $fc;
    protected $request;

    function setUp(): void
    {
        $this->request = new lmbHttpRequest('/');

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

        $this->fc
            ->registerFilter($f1)
            ->registerFilter($f2)
            ->registerFilter($mock_filter);

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
        $f3 = new OutputFilter3();
        $mock_filter = new InterceptingFilterStub();

        $fc1 = new lmbFilterChain();
        $fc1->registerFilter($f1);
        $fc1->registerFilter($mock_filter);

        $fc2 = new lmbFilterChain();
        $fc2->registerFilter($f2);
        $fc2->registerFilter($mock_filter);

        $fc = new lmbFilterChain();
        $fc
            ->registerFilter($fc1)
            ->registerFilter($fc2)
            ->registerFilter($f3)
            ->registerFilter($mock_filter);

        ob_start();

        $url = '/some_path';

        $response = $fc->process(
            new lmbHttpRequest($url, 'GET'), fn($request) => $request->getUri()->getPath()
        );

        $str = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($url, $response);
        $this->assertEquals('<filter1></filter1><filter2></filter2><filter3></filter3>', $str);
    }
}
