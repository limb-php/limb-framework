<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace web_agent\cases;

use limb\web_agent\src\lmbWebAgentKit;
use limb\web_agent\src\request\lmbNativeWebAgentRequest;
use limb\web_agent\src\request\lmbSocketWebAgentRequest;
use PHPUnit\Framework\TestCase;

/**
 * @package web_agent
 * @version $Id: lmbWebAgentValuesTest.php 40 2007-10-04 15:52:39Z
 */
class lmbWebAgentKitTest extends TestCase
{

    function testWebAgentKit()
    {
        $request = lmbWebAgentKit::createRequest();

        $this->assertInstanceOf(lmbSocketWebAgentRequest::class, $request);
    }

}
