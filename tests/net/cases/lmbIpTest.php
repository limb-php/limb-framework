<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\net\cases;

use PHPUnit\Framework\TestCase;
use limb\net\src\lmbIp;
use limb\core\src\exception\lmbException;

class lmbIpTest extends TestCase
{
    function testIsValid()
    {
        $this->assertTrue(lmbIp::isValid('127.0.0.1'));
        $this->assertTrue(lmbIp::isValid('255.255.255.255'));

        $this->assertFalse(lmbIp::isValid('wow'));
        $this->assertFalse(lmbIp::isValid('256.255.255.255'));
    }

    function testEncodeDecodeSigned()
    {
        //no overflow
        $this->assertEquals(2130706432, lmbIp::encode('127.0.0.0', lmbIp::SIGNED));
        $this->assertEquals('127.0.0.0', lmbIp::decode(2130706432));

        //with overflow
        if (!$this->_is64bit()) {
            $this->assertEquals(lmbIp::encode('128.0.0.0', lmbIp::SIGNED), (int)-2147483648);
            $this->assertEquals('128.0.0.0', lmbIp::decode(-2147483648));
        } else {
            $this->assertEquals(2147483648, lmbIp::encode('128.0.0.0', lmbIp::SIGNED));
            $this->assertEquals('128.0.0.0', lmbIp::decode(2147483648));
        }
    }

    function testEncodeDecodeUnsigned()
    {
        //no overflow
        $this->assertEquals(2130706432, lmbIp::encode('127.0.0.0', lmbIp::UNSIGNED));
        $this->assertEquals('127.0.0.0', lmbIp::decode(2130706432));

        //with overflow
        if (!$this->_is64bit()) {
            $this->assertEquals(2147483648.0, lmbIp::encode('128.0.0.0', lmbIp::UNSIGNED));
            $this->assertEquals('128.0.0.0', lmbIp::decode(2147483648));
        } else {
            $this->assertEquals(2147483648, lmbIp::encode('128.0.0.0', lmbIp::UNSIGNED));
            $this->assertEquals('128.0.0.0', lmbIp::decode(2147483648));
        }
    }

    function testEncodeDecodeUnsignedString()
    {
        //no overflow
        $this->assertEquals('2130706432', lmbIp::encode('127.0.0.0', lmbIp::USTRING));
        $this->assertEquals('127.0.0.0', lmbIp::decode('2130706432'));

        //with overflow
        $this->assertEquals('2147483648', lmbIp::encode('128.0.0.0', lmbIp::USTRING));
        $this->assertEquals('128.0.0.0', lmbIp::decode('2147483648'));
    }

    function testEncodeIpRangeFailure()
    {
        try {
            lmbIp::encodeIpRange('bla', 'foo');
            $this->fail();
        } catch (lmbException $e) {
            $this->assertTrue(true);
        }

        try {
            lmbIp::encodeIpRange('127.0.0.1', 'foo');
            $this->fail();
        } catch (lmbException $e) {
            $this->assertTrue(true);
        }

        try {
            lmbIp::encodeIpRange('bla', '127.0.0.1');
            $this->fail();
        } catch (lmbException $e) {
            $this->assertTrue(true);
        }
    }

    function testEncodeIpRangeSigned()
    {
        $ip_list = lmbIp::encodeIpRange('192.168.0.1', '192.168.10.10', lmbIp::SIGNED);

        $this->assertEquals((pow(256, 1) * 10 + pow(256, 0) * 10) - (pow(256, 1) * 0 + pow(256, 0) * 1) + 1, sizeof($ip_list));
        $this->assertFalse(array_search(lmbIp::encode('192.168.0.0', lmbIp::SIGNED), $ip_list));
        $this->assertEquals(0, array_search(lmbIp::encode('192.168.0.1', lmbIp::SIGNED), $ip_list));
        $this->assertNotEquals(false, array_search(lmbIp::encode('192.168.0.255', lmbIp::SIGNED), $ip_list));
        $this->assertNotEquals(false, array_search(lmbIp::encode('192.168.10.10', lmbIp::SIGNED), $ip_list));
        $this->assertEquals(false, array_search(lmbIp::encode('192.168.10.11', lmbIp::SIGNED), $ip_list));
    }

    function testEncodeIpRangeUnsigned()
    {
        $ip_list = lmbIp::encodeIpRange('192.168.0.1', '192.168.10.10', lmbIp::UNSIGNED);

        $this->assertEquals((pow(256, 1) * 10 + pow(256, 0) * 10) - (pow(256, 1) * 0 + pow(256, 0) * 1) + 1, sizeof($ip_list));
        $this->assertFalse(array_search(lmbIp::encode('192.168.0.0', lmbIp::UNSIGNED), $ip_list));
        $this->assertEquals(0, array_search(lmbIp::encode('192.168.0.1', lmbIp::UNSIGNED), $ip_list));
        $this->assertNotEquals(false, array_search(lmbIp::encode('192.168.0.255', lmbIp::UNSIGNED), $ip_list));
        $this->assertNotEquals(false, array_search(lmbIp::encode('192.168.10.10', lmbIp::UNSIGNED), $ip_list));
        $this->assertEquals(false, array_search(lmbIp::encode('192.168.10.11', lmbIp::UNSIGNED), $ip_list));
    }

    function testEncodeIpRangeUnsignedString()
    {
        $ip_list = lmbIp::encodeIpRange('192.168.0.1', '192.168.10.10', lmbIp::USTRING);

        $this->assertEquals((pow(256, 1) * 10 + pow(256, 0) * 10) - (pow(256, 1) * 0 + pow(256, 0) * 1) + 1, sizeof($ip_list));
        $this->assertFalse(array_search(lmbIp::encode('192.168.0.0', lmbIp::USTRING), $ip_list));
        $this->assertEquals(0, array_search(lmbIp::encode('192.168.0.1', lmbIp::USTRING), $ip_list));
        $this->assertNotEquals(false, array_search(lmbIp::encode('192.168.0.255', lmbIp::USTRING), $ip_list));
        $this->assertNotEquals(false, array_search(lmbIp::encode('192.168.10.10', lmbIp::USTRING), $ip_list));
        $this->assertFalse(array_search(lmbIp::encode('192.168.10.11', lmbIp::USTRING), $ip_list));
    }

    protected function _is64bit(): bool
    {
        return PHP_INT_SIZE == 8;
    }

}
