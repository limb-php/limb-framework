<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\dbal\cases\nondriver\criteria;

require('tests/dbal/common.inc.php');

use PHPUnit\Framework\TestCase;
use limb\dbal\src\criteria\lmbSQLFieldBetweenCriteria;

class lmbSQLFieldBetweenCriteriaTest extends TestCase
{
  var $conn;
  var $query;

  function setUp(): void
  {
    $this->conn = new \ConnectionTestStub();
  }

  function testSimple()
  {
    $c1 = new lmbSQLFieldBetweenCriteria('a', 1, 10);

    $this->assertEquals($c1->toStatementString($values, $this->conn),
                       "'a' BETWEEN :pfa0: AND :pta0:");

    $this->assertEquals($values, array('pfa0' => 1,
                                      'pta0' => 10));
  }

  function _testNested()
  {
    $c1 = new lmbSQLFieldBetweenCriteria('a', 1, 10);
    $c2 = new lmbSQLFieldBetweenCriteria('a', 20, 30);
    $c1->addOr($c2);

    $this->assertEquals($c1->toStatementString($values, $this->conn),
                       "('a' BETWEEN :pfa0: AND :pta0: OR a BETWEEN :pfa1: AND :pta1:)");

    $this->assertEquals($values, array('pfa0' => 1,
                                      'pta0' => 10,
                                      'pfa1' => 20,
                                      'pta1' => 30));
  }
}
