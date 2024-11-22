<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\web_agent\cases\agent\liveinternet;

use PHPUnit\Framework\TestCase;
use limb\web_agent\src\agent\liveinternet\lmbLiveInternetValues;

require_once dirname(__FILE__) . '/../../../.setup.php';

/**
 * @package web_agent
 * @version $Id: lmbLiveInternetValuesTest.php 43 2007-10-05 15:33:11Z
 */
class lmbLiveInternetValuesTest extends TestCase
{

    function testBuildQuery()
    {
        $arr = array(
            'test' => 'val',
            'test1' => 'val1',
            'id' => array(9, 7, 5, 0));
        $vals = new lmbLiveInternetValues($arr);

        $this->assertEquals($vals->buildQuery(), 'test=val;test1=val1;id=9;id=7;id=5;id=0');
    }

}
