<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\web_cache\cases;

use limb\web_cache\src\lmbFullPageCacheUser;
use limb\web_cache\src\lmbFullPageCacheRequest;
use limb\net\src\lmbHttpRequest;
use PHPUnit\Framework\TestCase;

require_once (dirname(__FILE__) . '/init.inc.php');

class lmbFullPageCacheRequestTest extends TestCase
{
    function testGetHash()
    {
        $user = new lmbFullPageCacheUser();
        $http_request = new lmbHttpRequest('https://test.com', 'GET', array(), array(), array(), array());

        $request = new lmbFullPageCacheRequest($http_request, $user);

        $this->assertEquals($request->getHash(), '/');
    }

    function testGetHashAlphabeticSorting()
    {
        $user = new lmbFullPageCacheUser(array(2 => 'test', 3 => 'admin'));
        $http_request = new lmbHttpRequest('https://test.com/path?z=3&a=1&c[d]=2', 'GET', array(), array(), array(), array());

        $request = new lmbFullPageCacheRequest($http_request, $user);

        $this->assertEquals($request->getHash(),
            '/path_' . md5(serialize(array('a' => '1', 'c[d]' => '2', 'z' => '3')) .
                serialize(array('admin', 'test'))) . '/');
    }
}
