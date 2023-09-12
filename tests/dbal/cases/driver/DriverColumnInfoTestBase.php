<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace Tests\dbal\cases\driver;

abstract class DriverColumnInfoTestBase extends DriverMetaTestBase
{
    protected $table;

  function setUp(): void
  {
    $dbinfo = $this->connection->getDatabaseInfo();
    $this->table = $dbinfo->getTable('standard_types');
  }

  function tearDown(): void
  {
    unset($this->table);
    parent::tearDown();
  }
}
