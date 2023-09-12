<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace Tests\dbal\cases\nondriver\dump;

use limb\dbal\src\drivers\mysql\lmbMysqlConnection;
use limb\dbal\src\dump\lmbMysqlDumpLoader;
use limb\toolkit\src\lmbToolkit;

class lmbMysqlDumpLoaderTest extends lmbSQLDumpLoaderTestCase
{
  function setUp(): void
  {
      if( !is_a(lmbToolkit::instance()->getDefaultDbConnection(), lmbMysqlConnection::class) )
        $this->markTestSkipped("lmbMysqlDumpLoader tests skipped, mysql connection required");

      parent::setUp();
  }

  function _createLoader($file=null)
  {
    return new lmbMysqlDumpLoader($file);
  }

  function testFullBlownMysqlDump()
  {
    $sql = <<< EOD
/*
SQLyog Enterprise v4.06 RC1
Host - 4.0.12-max-debug : Database - all-limb-tests
*********************************************************************
Server version : 4.0.12-max-debug
*/

SET FOREIGN_KEY_CHECKS=0;

create database if not exists `foo`;

/*Table structure for table `bar` */

drop table if exists `bar`;

CREATE TABLE `bar` (
  `id` int(11) NOT null auto_increment,
  `url` varchar(255) NOT null default '',
  `description` varchar(255) default null,
  `img_src` varchar(255) default null,
  PRIMARY KEY  (`id`),
  KEY `id` (`id`,`oid`)
) TYPE=InnoDB;

/*Data for the table `baz` */;
LOCK TABLES `baz` WRITE;

UNLOCK TABLES;

INSERT INTO `article` VALUES (8,101,2,'TemplateView','Template View','wiki');
EOD;

    $this->_writeDump($sql, $this->file_path);

    $loader = $this->_createLoader($this->file_path);

    $statements = $loader->getStatements();

    $this->assertEquals(7, sizeof($statements));

    $this->assertEquals('SET FOREIGN_KEY_CHECKS=0', $statements[0]);
    $this->assertEquals('create database if not exists `foo`', $statements[1]);
    $this->assertEquals('drop table if exists `bar`', $statements[2]);
    $this->assertEquals("CREATE TABLE `bar` (
  `id` int(11) NOT null auto_increment,
  `url` varchar(255) NOT null default '',
  `description` varchar(255) default null,
  `img_src` varchar(255) default null,
  PRIMARY KEY  (`id`),
  KEY `id` (`id`,`oid`)
) TYPE=InnoDB", $statements[3]);
    $this->assertEquals('LOCK TABLES `baz` WRITE', $statements[4]);
    $this->assertEquals('UNLOCK TABLES', $statements[5]);
    $this->assertEquals("INSERT INTO `article` VALUES (8,101,2,'TemplateView','Template View','wiki')", $statements[6]);

    $this->assertEquals(array('article'), $loader->getAffectedTables());
  }
}
