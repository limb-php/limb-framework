<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\web_agent\cases;

use limb\web_agent\src\lmbWebAgentValues;
use PHPUnit\Framework\TestCase;

/**
 * @package web_agent
 * @version $Id: lmbWebAgentValuesTest.php 40 2007-10-04 15:52:39Z
 */
class lmbWebAgentValuesTest extends TestCase
{

    function testBuildQuery()
    {
        $arr = array('test' => 'val', 'test1' => 'val1');
        $vals = new lmbWebAgentValues($arr);

        $this->assertEquals($vals->buildQuery(), http_build_query($arr));
    }

}
