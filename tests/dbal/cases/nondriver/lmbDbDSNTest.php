<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace Tests\dbal\cases\nondriver;

require_once(dirname(__FILE__) . '/.setup.php');

use PHPUnit\Framework\TestCase;
use limb\dbal\src\lmbDbDSN;
use limb\core\src\exception\lmbException;

class lmbDbDSNTest extends TestCase
{
  function testMalformedStringThrowsException()
  {
    try
    {
      $dsn = new lmbDbDSN('mysql:///');
      $this->fail();
    }
    catch(lmbException $e){
        $this->assertTrue(true);
    }
  }

  function testConstructUsingString()
  {
    $dsn = new lmbDbDSN($str = 'mysql://wow:here@localhost/db');
    $this->assertEquals('mysql', $dsn->getDriver());
    $this->assertEquals('wow', $dsn->getUser());
    $this->assertEquals('here', $dsn->getPassword());
    $this->assertEquals('localhost', $dsn->getHost());
    $this->assertEquals('db', $dsn->getDatabase());
    $this->assertEquals($dsn->toString(), $str);
  }

  function testConstructUsingStringWithPort()
  {
    $dsn = new lmbDbDSN($str = 'mysql://wow:here@localhost:8080/db');
    $this->assertEquals('mysql', $dsn->getDriver());
    $this->assertEquals('wow', $dsn->getUser());
    $this->assertEquals('here', $dsn->getPassword());
    $this->assertEquals('localhost', $dsn->getHost());
    $this->assertEquals(8080, $dsn->getPort());
    $this->assertEquals('db', $dsn->getDatabase());
    $this->assertEquals($dsn->toString(), $str);
  }

  function testConstructUsingStringWithExtraParameters()
  {
    $dsn = new lmbDbDSN($str = 'mysql://wow:here@localhost/db?param1=hey&param2=wow');
    $this->assertEquals('mysql', $dsn->getDriver());
    $this->assertEquals('wow', $dsn->getUser());
    $this->assertEquals('here', $dsn->getPassword());
    $this->assertEquals('localhost', $dsn->getHost());
    $this->assertEquals('db', $dsn->getDatabase());

    $this->assertEquals('hey', $dsn->getParam1());//extra parameters
    $this->assertEquals('wow', $dsn->getParam2());

    $this->assertEquals($dsn->toString(), $str);
  }

  function testConstructUsingArray()
  {
    $dsn = new lmbDbDSN(array('driver' => 'mysql',
                              'host' => 'localhost',
                              'user' => 'wow',
                              'password' => 'here',
                              'database' => 'db',
                              'port' => 8080));

    $this->assertEquals('mysql', $dsn->getDriver());
    $this->assertEquals('wow', $dsn->getUser());
    $this->assertEquals('here', $dsn->getPassword());
    $this->assertEquals('localhost', $dsn->getHost());
    $this->assertEquals(8080, $dsn->getPort());
    $this->assertEquals('db', $dsn->getDatabase());
    $this->assertEquals('mysql://wow:here@localhost:8080/db', $dsn->toString());
  }

  function testConstructUsingArrayWithExtraParameters()
  {
    $dsn = new lmbDbDSN(array('driver' => 'mysql',
                              'host' => 'localhost',
                              'user' => 'wow',
                              'password' => 'here',
                              'database' => 'db',
                              'port' => 8080,
                              array('param1' => 'hey',
                                    'param2' => 'wow')));

    $this->assertEquals('mysql', $dsn->getDriver());
    $this->assertEquals('wow', $dsn->getUser());
    $this->assertEquals('here', $dsn->getPassword());
    $this->assertEquals('localhost', $dsn->getHost());
    $this->assertEquals(8080, $dsn->getPort());
    $this->assertEquals('db', $dsn->getDatabase());

    $this->assertEquals('hey', $dsn->getParam1());//extra parameters
    $this->assertEquals('wow', $dsn->getParam2());

    $this->assertEquals('mysql://wow:here@localhost:8080/db?param1=hey&param2=wow', $dsn->toString());
  }

  function testBuildUri()
  {
    $dsn = new lmbDbDSN(array('driver' => 'mysql', 'host' => 'localhost'));
    $this->assertEquals('mysql://localhost/', $dsn->buildUri()->toString());

    $dsn->host = 'somehost';
    $this->assertEquals('mysql://somehost/', $dsn->buildUri()->toString());
  }
}
