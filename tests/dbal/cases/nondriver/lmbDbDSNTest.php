<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\dbal\cases\nondriver;

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
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testConstructUsingString()
  {
    $dsn = new lmbDbDSN($str = 'mysql://wow:here@localhost/db');
    $this->assertEquals($dsn->getDriver(), 'mysql');
    $this->assertEquals($dsn->getUser(), 'wow');
    $this->assertEquals($dsn->getPassword(), 'here');
    $this->assertEquals($dsn->getHost(), 'localhost');
    $this->assertEquals($dsn->getDatabase(), 'db');
    $this->assertEquals($dsn->toString(), $str);
  }

  function testConstructUsingStringWithPort()
  {
    $dsn = new lmbDbDSN($str = 'mysql://wow:here@localhost:8080/db');
    $this->assertEquals($dsn->getDriver(), 'mysql');
    $this->assertEquals($dsn->getUser(), 'wow');
    $this->assertEquals($dsn->getPassword(), 'here');
    $this->assertEquals($dsn->getHost(), 'localhost');
    $this->assertEquals($dsn->getPort(), 8080);
    $this->assertEquals($dsn->getDatabase(), 'db');
    $this->assertEquals($dsn->toString(), $str);
  }

  function testConstructUsingStringWithExtraParameters()
  {
    $dsn = new lmbDbDSN($str = 'mysql://wow:here@localhost/db?param1=hey&param2=wow');
    $this->assertEquals($dsn->getDriver(), 'mysql');
    $this->assertEquals($dsn->getUser(), 'wow');
    $this->assertEquals($dsn->getPassword(), 'here');
    $this->assertEquals($dsn->getHost(), 'localhost');
    $this->assertEquals($dsn->getDatabase(), 'db');

    $this->assertEquals($dsn->getParam1(), 'hey');//extra parameters
    $this->assertEquals($dsn->getParam2(), 'wow');

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

    $this->assertEquals($dsn->getDriver(), 'mysql');
    $this->assertEquals($dsn->getUser(), 'wow');
    $this->assertEquals($dsn->getPassword(), 'here');
    $this->assertEquals($dsn->getHost(), 'localhost');
    $this->assertEquals($dsn->getPort(), 8080);
    $this->assertEquals($dsn->getDatabase(), 'db');
    $this->assertEquals($dsn->toString(), 'mysql://wow:here@localhost:8080/db');
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

    $this->assertEquals($dsn->getDriver(), 'mysql');
    $this->assertEquals($dsn->getUser(), 'wow');
    $this->assertEquals($dsn->getPassword(), 'here');
    $this->assertEquals($dsn->getHost(), 'localhost');
    $this->assertEquals($dsn->getPort(), 8080);
    $this->assertEquals($dsn->getDatabase(), 'db');

    $this->assertEquals($dsn->getParam1(), 'hey');//extra parameters
    $this->assertEquals($dsn->getParam2(), 'wow');

    $this->assertEquals($dsn->toString(), 'mysql://wow:here@localhost:8080/db?param1=hey&param2=wow');
  }

  function testBuildUri()
  {
    $dsn = new lmbDbDSN(array('driver' => 'mysql', 'host' => 'localhost'));
    $this->assertEquals($dsn->buildUri()->toString(), 'mysql://localhost/');

    $dsn->host = 'somehost';
    $this->assertEquals($dsn->buildUri()->toString(), 'mysql://somehost/');
  }
}
