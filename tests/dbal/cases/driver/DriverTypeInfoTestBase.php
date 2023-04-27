<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\dbal\cases\driver;
use PHPUnit\Framework\TestCase;

abstract class DriverTypeInfoTestBase extends TestCase
{
  private $typeInfo;
  private $columnList;
  private $queryStmtClass;
  private $recordClass;

  function __construct($queryStmtClass, $recordClass)
  {
    $this->queryStmtClass = $queryStmtClass;
    $this->recordClass = $recordClass;
  }

  function setUp(): void
  {
    $this->columnList = $this->typeInfo->getColumnTypeList();

    parent::setUp();
  }

  function testGetColumnTypeAccessors()
  {
    $mapping = $this->typeInfo->getColumnTypeAccessors();
    foreach($this->columnList as $columnType)
    {
      $this->assertTrue(isset($mapping[$columnType]));
    }
    foreach($mapping as $col => $name)
    {
      $this->assertTrue(in_array($col, $this->columnList));
      $this->assertTrue(is_callable(array($this->queryStmtClass, $name)), "'$name' is not callable in {$this->queryStmtClass}");
    }
  }

  function testGetColumnTypeGetters()
  {
    $mapping = $this->typeInfo->getColumnTypeGetters();
    foreach($this->columnList as $columnType)
    {
      $this->assertTrue(isset($mapping[$columnType]));
    }
    foreach($mapping as $prop => $name)
    {
      $this->assertTrue(in_array($prop, $this->columnList));
      $this->assertTrue(is_callable(array($this->recordClass, $name)), "'$name' is not callable in {$this->recordClass}");
    }
  }
}
