<?php

namespace tests\dbal\cases\toolkit;

use limb\dbal\src\drivers\lmbDbCachedInfo;
use limb\dbal\src\drivers\mysql\lmbMysqlDbInfo;
use PHPUnit\Framework\TestCase;
use limb\core\src\lmbEnv;
use limb\core\src\lmbSet;
use limb\config\src\lmbConf;
use limb\toolkit\src\lmbToolkit;
use limb\dbal\src\toolkit\lmbDbTools;
use limb\dbal\src\lmbDbDSN;
use limb\core\src\exception\lmbException;

require_once(dirname(__FILE__) . '/.setup.php');

class ExceptionalDbConfStub extends lmbConf
{
    function __construct()
    {

    }

    function get($name, $default = null)
    {
        throw new lmbException("Ooops!");
    }
}

class lmbDbToolsTest extends TestCase
{
    var $tools;
    var $config;
    var $conn;

    function setUp(): void
    {
        parent::setUp();

        $this->tools = new lmbDbTools();
        $this->conn = $this->tools->getDefaultDbConnection();
        $this->config = array(
            'dsn' => 'mysql://root:test@localhost/hello_from_foo?charset=cp1251',
            'another_dsn' => 'sqlite://kraynopp:pasha@ksu/kadrs?charset=utf8'
        );
        lmbToolkit::instance()->setConf('db', new lmbSet($this->config));
        $this->tools->setDefaultDbConnection($this->tools->createDbConnection(new lmbDbDSN($this->config['dsn'])));
    }

    function tearDown(): void
    {
        $this->tools->setDefaultDbConnection($this->conn);
    }

    function testGetDbDSNByName()
    {
        $this->assertEquals($this->tools->getDbDSNByName('another_dsn'), new lmbDbDSN($this->config['another_dsn']));
    }

    function testGetDefaultDbDSN()
    {
        $this->assertEquals(
            $this->tools->getDefaultDbDSN(),
            $this->tools->getDbDSNByName('dsn')
        );
    }

    function testIsDefaultDbDSNAvailable()
    {
        $tools = new lmbDbTools();
        $tools->setDefaultDbDSN("mysql://localhost/test");
        $this->assertTrue($tools->isDefaultDbDSNAvailable());

        $toolkit = lmbToolkit::save();
        $tools = new lmbDbTools();
        $toolkit->setConf('db', new ExceptionalDbConfStub());
        $this->assertFalse($tools->isDefaultDbDSNAvailable());
        lmbToolkit:: restore();
    }

    function testSetDbDSNByName()
    {
        $this->assertEquals($this->tools->getDbDSNByName('another_dsn'), new lmbDbDSN($this->config['another_dsn']));
        $dsn = new lmbDbDSN($this->config['dsn']);
        $this->tools->setDbDSNByName('another_dsn', $dsn);
        $this->assertEquals($this->tools->getDbDSNByName('another_dsn'), new lmbDbDSN($this->config['dsn']));
    }

    function testGetDbConnectionByName()
    {
        $connection = $this->tools->createDbConnection(new lmbDbDSN($this->config['another_dsn']));
        $this->assertEquals($connection, $this->tools->getDbConnectionByName('another_dsn'));
    }

    function testSetDbConnectionByName()
    {
        $dsn = new lmbDbDSN($this->config['dsn']);
        $another_dsn = new lmbDbDSN($this->config['another_dsn']);

        $connection = $this->tools->createDbConnection($dsn);
        $another_connection = $this->tools->createDbConnection($another_dsn);

        $this->assertEquals($connection, $this->tools->getDbConnectionByName('dsn'));

        $this->tools->setDbConnectionByName('dsn', $another_connection);

        $this->assertEquals($another_connection, $this->tools->getDbConnectionByName('dsn'));
    }

    function testGetDbConnectionByDsn()
    {
        $connection = $this->tools->createDbConnection(new lmbDbDSN($this->config['another_dsn']));
        $this->assertEquals($connection, $this->tools->getDbConnectionByDsn(new lmbDbDSN($this->config['another_dsn'])));
    }

    function testSetDbConnectionByDsn()
    {
        $dsn = new lmbDbDSN($this->config['dsn']);
        $another_dsn = new lmbDbDSN($this->config['another_dsn']);

        $connection = $this->tools->createDbConnection($dsn);
        $another_connection = $this->tools->createDbConnection($another_dsn);

        $this->assertEquals($connection, $this->tools->getDbConnectionByDsn($dsn));

        $this->tools->setDbConnectionByDsn($dsn, $another_connection);

        $this->assertEquals($another_connection, $this->tools->getDbConnectionByDsn($dsn));
    }

    function testGettingConnectionsByNameAndDSNReturnsTheSameConnectionObject()
    {
        $connection_by_name = $this->tools->getDbConnectionByName('another_dsn');
        $connection_by_dsn = $this->tools->getDbConnectionByDsn($this->config['another_dsn']);
        $this->assertEquals($connection_by_name, $connection_by_dsn);
    }

    function testGetDbInfo_cache_global_negative()
    {
        lmbEnv::set('LIMB_CACHE_DB_META_IN_FILE', false);
        $conn = $this->tools->getDbConnectionByDsn('mysql://root:test@localhost/hello_from_foo?charset=cp1251&version=1');
        $this->assertIsObject($this->tools->getDbInfo($conn), lmbMysqlDbInfo::class);
    }

    function testGetDbInfo_cache_global_positive()
    {
        lmbEnv::set('LIMB_CACHE_DB_META_IN_FILE', true);
        $conn = $this->tools->getDbConnectionByDsn('mysql://root:test@localhost/hello_from_foo?charset=cp1251&version=2');
        $this->assertIsObject($this->tools->getDbInfo($conn), lmbDbCachedInfo::class);
    }

    function testGetDbInfo_cache_in_conf_negative()
    {
        lmbEnv::remove('LIMB_CACHE_DB_META_IN_FILE');

        $config = new lmbSet($this->config);
        $config['cache_db_info'] = false;
        lmbToolkit::instance()->setConf('db', $config);

        $conn = $this->tools->getDbConnectionByDsn('mysql://root:test@localhost/hello_from_foo?charset=cp1251&version=4');
        $this->assertIsObject($this->tools->getDbInfo($conn), lmbMysqlDbInfo::class);
    }

    function testGetDbInfo_cache_in_conf_positive()
    {
        lmbEnv::remove('LIMB_CACHE_DB_META_IN_FILE');

        $config = new lmbSet($this->config);
        $config['cache_db_info'] = true;
        lmbToolkit::instance()->setConf('db', $config);

        $conn = $this->tools->getDbConnectionByDsn('mysql://root:test@localhost/hello_from_foo?charset=cp1251&version=3');
        $this->assertIsObject($this->tools->getDbInfo($conn), lmbDbCachedInfo::class);
    }
}
