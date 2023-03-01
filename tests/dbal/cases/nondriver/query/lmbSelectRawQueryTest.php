<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\dbal\cases\nondriver\query;

require_once('tests/dbal/common.inc.php');

use limb\dbal\src\drivers\lmbDbStatementInterface;
use limb\toolkit\src\lmbToolkit;
use PHPUnit\Framework\TestCase;
use limb\dbal\src\criteria\lmbSQLRawCriteria;
use limb\dbal\src\criteria\lmbSQLFieldCriteria;
use limb\dbal\src\query\lmbSelectRawQuery;
use limb\dbal\src\drivers\lmbDbConnectionInterface;
use limb\core\src\exception\lmbException;
use tests\dbal\ConnectionTestStub;

class lmbSelectRawQueryTest extends TestCase
{
  protected $conn;

  function setUp(): void
  {
    //this stub uses ' quoting for simpler testing
    $this->conn = new ConnectionTestStub();
  }

  function testSimpleSelect()
  {
    $sql = new lmbSelectRawQuery('SELECT * FROM test', $this->conn);

    $this->assertEquals('SELECT * FROM test', $sql->toString());
  }

  function testReplaceFieldsHintByDefault()
  {
    $sql = new lmbSelectRawQuery('SELECT %fields% FROM test', $this->conn);
    $this->assertEquals('SELECT * FROM test', $sql->toString());
  }

  function testReplaceFieldsHintWhenFieldsExistInTemplate()
  {
    $sql = new lmbSelectRawQuery("SELECT t3 \n%fields%,t4 FROM test", $this->conn);

    $this->assertEquals("SELECT t3 \n,t4 FROM test", $sql->toString());
  }

  function testAddFieldWhenNoFieldsExistInTemplate()
  {
    $sql = new lmbSelectRawQuery('SELECT %fields% FROM test', $this->conn);

    $sql->addField('t1');
    $sql->addField('t2');

    $this->assertEquals("SELECT 't1','t2' FROM test", $sql->toString());
  }

  function testAddRawFieldWhenNoFieldsExistInTemplate()
  {
    $sql = new lmbSelectRawQuery('SELECT %fields% FROM test', $this->conn);

    $sql->addRawField('t1');
    $sql->addRawField('t2');

    $this->assertEquals("SELECT t1,t2 FROM test", $sql->toString());
  }

  function testAddFieldWhenFieldsExistInTemplate()
  {
    $sql = new lmbSelectRawQuery("SELECT t3 \n%fields%,t4 FROM test", $this->conn);

    $sql->addField('t1');
    $sql->addField('t2');

    $this->assertEquals("SELECT t3 \n,'t1','t2',t4 FROM test", $sql->toString());
  }

  function testAddRawFieldWhenFieldsExistInTemplate()
  {
    $sql = new lmbSelectRawQuery("SELECT t3 \n%fields%,t4 FROM test", $this->conn);

    $sql->addRawField('t1');
    $sql->addRawField('t2');

    $this->assertEquals("SELECT t3 \n,t1,t2,t4 FROM test", $sql->toString());
  }

  function testAddFieldWithAlias()
  {
    $sql = new lmbSelectRawQuery('SELECT %fields% FROM test', $this->conn);

    $sql->addField('t1', 'a1');
    $sql->addField('t2', 'a2');

    $this->assertEquals("SELECT 't1' as 'a1','t2' as 'a2' FROM test", $sql->toString());
  }
  
  function testAddRawFieldWithAlias()
  {
    $sql = new lmbSelectRawQuery('SELECT %fields% FROM test', $this->conn);

    $sql->addRawField('t1', 'a1');
    $sql->addRawField('t2', 'a2');

    $this->assertEquals("SELECT t1 as a1,t2 as a2 FROM test", $sql->toString());
  }

  function testMixAddingRawAndRegularFields()
  {
    $sql = new lmbSelectRawQuery("SELECT %fields% FROM test", $this->conn);

    $sql->addRawField('t1');
    $sql->addField('t2', 'a2');
    $sql->addRawField('t3', 'a3');
    $sql->addField('t4');

    $this->assertEquals("SELECT 't2' as 'a2','t4',t1,t3 as a3 FROM test", $sql->toString());
  }
  
  function testMixAddingRawAndRegularFieldsWhenFieldsExistInTemplate()
  {
    $sql = new lmbSelectRawQuery("SELECT a \n%fields%,b FROM test", $this->conn);

    $sql->addRawField('t1');
    $sql->addField('t2', 'a2');
    $sql->addRawField('t3', 'a3');
    $sql->addField('t4');

    $this->assertEquals("SELECT a \n,'t2' as 'a2','t4',t1,t3 as a3,b FROM test", $sql->toString());
  }
  
  function testAddStarredFieldFromTable()
  {
    $sql = new lmbSelectRawQuery('SELECT %fields% FROM test', $this->conn);
    $sql->addField('t1.*');

    $this->assertEquals('SELECT t1.* FROM test', $sql->toString());
  }

  function testReplaceTableHintInTemplate()
  {
    $sql = new lmbSelectRawQuery('SELECT * FROM test %tables%', $this->conn);

    $this->assertEquals('SELECT * FROM test', $sql->toString());
  }

  function testAddTable()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test \n\t%tables%", $this->conn);

    $sql->addTable('test2');
    $sql->addTable('test3');

    $this->assertEquals("SELECT * FROM test \n\t,'test2','test3'", $sql->toString());
  }

  function testAddTableWithAlias()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test \n\t%tables%", $this->conn);

    $sql->addTable('test2', 't2');
    $sql->addTable('test3');

    $this->assertEquals("SELECT * FROM test \n\t,'test2' 't2','test3'", $sql->toString());
  }

  function testAddSameTable()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test \n\t%tables%", $this->conn);

    $sql->addTable('test2', 'a');
    $sql->addTable('test2', 'b');

    $this->assertEquals("SELECT * FROM test \n\t,'test2' 'a','test2' 'b'", $sql->toString());
  }

  function testAddLeftJoin()
  {
    $sql = new lmbSelectRawQuery('SELECT * FROM test %left_join%', $this->conn);

    $sql->addLeftJoin('article', 'id', 'test', 'article_id');

    $this->assertEquals("SELECT * FROM test LEFT JOIN 'article' ON 'article.id'='test.article_id'",
        $sql->toString());
  }

  function testAddLeftJoinTwiceForTheSameTable()
  {
    $sql = new lmbSelectRawQuery('SELECT * FROM test %left_join%', $this->conn);

    $sql->addLeftJoin('article', 'id', 'test', 'article_id');
    $sql->addLeftJoin('article', 'id', 'test', 'other_article_id', 'next_article');

    $this->assertEquals($sql->toString(),
                       "SELECT * FROM test LEFT JOIN 'article' ON 'article.id'='test.article_id'".
                       " LEFT JOIN 'article' AS 'next_article' ON 'next_article.id'='test.other_article_id'");
  }
  
  function testEmptyCondition()
  {
    $sql = new lmbSelectRawQuery('SELECT * FROM test %where%', $this->conn);

    $this->assertEquals('SELECT * FROM test',
        $sql->toString());
  }

  function testAddCondition()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test WHERE \n%where%", $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));

    $this->assertEquals("SELECT * FROM test WHERE \nc1=:c1:",
        $sql->toString());
  }

  function testAddConditionNoWhereClause()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test \n%where%", $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));

    $this->assertEquals("SELECT * FROM test \nWHERE c1=:c1:",
        $sql->toString());
  }

  function testAddConditionNoHintThrowsException()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test WHERE 1=1", $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));
    try
    {
      $sql->toString();
      $this->fail();
    }
    catch(lmbException $e)
    {
      $this->assertTrue(true);
    }
  }

  function testAddSeveralConditions()
  {
    $sql = new lmbSelectRawQuery('SELECT * FROM test %where%', $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addCriteria(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEquals('SELECT * FROM test WHERE c1=:c1: AND c2=:c2:',
        $sql->toString());
  }

  function testAddConditionToExistingConditions()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test WHERE t1=t1\n %where%", $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addCriteria(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEquals("SELECT * FROM test WHERE t1=t1\n AND c1=:c1: AND c2=:c2:",
        $sql->toString());
  }

  function testAddConditionToExistingConditionsWithOrder()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test WHERE t1=t1\n\n %where% \n\tORDER BY t1", $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addCriteria(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEquals("SELECT * FROM test WHERE t1=t1\n\n AND c1=:c1: AND c2=:c2: \n\tORDER BY t1",
        $sql->toString());
  }

  function testAddConditionToExistingConditionsWithGroup()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test WHERE t1=t1\n\n %where% \n\tGROUP BY t1", $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addCriteria(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEquals("SELECT * FROM test WHERE t1=t1\n\n AND c1=:c1: AND c2=:c2: \n\tGROUP BY t1",
        $sql->toString());
  }

  function testEmptyOrder()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test \n%order%", $this->conn);

    $this->assertEquals('SELECT * FROM test',
        $sql->toString());
  }

  function testAddOrderNoOrderClause()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test \n%order%", $this->conn);

    $sql->addOrder('t1');
    $sql->addOrder('t2', 'DESC');

    $this->assertEquals("SELECT * FROM test \nORDER BY 't1' ASC,'t2' DESC",
        $sql->toString());
  }

  function testAddRawOrderNoOrderClause()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test \n%order%", $this->conn);

    $sql->addRawOrder('rand1()');
    $sql->addRawOrder('rand2()');

    $this->assertEquals("SELECT * FROM test \nORDER BY rand1(),rand2()",
        $sql->toString());
  }

  function testAddOrderAsArray()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test \n%order%", $this->conn);

    $sql->addOrder(array('t1' => 'ASC', 't2' => 'DESC'));
    $this->assertEquals("SELECT * FROM test \nORDER BY 't1' ASC,'t2' DESC", $sql->toString());
  }
  
  function testAddOrderWithOrderClause()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test ORDER BY\n %order%", $this->conn);

    $sql->addOrder('t1');
    $sql->addOrder('t2', 'DESC');

    $this->assertEquals("SELECT * FROM test ORDER BY\n 't1' ASC,'t2' DESC",
        $sql->toString());
  }

  function testAddRawOrderWithOrderClause()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test ORDER BY\n %order%", $this->conn);

    $sql->addRawOrder('rand1()');
    $sql->addRawOrder('rand2()');

    $this->assertEquals("SELECT * FROM test ORDER BY\n rand1(),rand2()",
        $sql->toString());
  }

  function testAddOrderWithOrderClause2()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test ORDER BY t0 DESC\n %order%", $this->conn);

    $sql->addOrder('t1');
    $sql->addOrder('t2', 'DESC');

    $this->assertEquals("SELECT * FROM test ORDER BY t0 DESC\n ,'t1' ASC,'t2' DESC",
        $sql->toString());
  }

  function testAddOrderWithOrderClause3()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test ORDER BY t0 DESC\n %order%", $this->conn);

    $this->assertEquals('SELECT * FROM test ORDER BY t0 DESC',
        $sql->toString());
  }

  function testMixRawAndRegularOrder()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test \n%order%", $this->conn);

    $sql->addRawOrder('rand1()');
    $sql->addOrder('t1');
    $sql->addRawOrder('rand2()');
    $sql->addOrder('t2', 'DESC');

    $this->assertEquals("SELECT * FROM test \nORDER BY rand1(),'t1' ASC,rand2(),'t2' DESC",
        $sql->toString());
  }


  function testNoGroupsAdded()
  {
    $sql = new lmbSelectRawQuery('SELECT * FROM test', $this->conn);

    $this->assertEquals('SELECT * FROM test',
        $sql->toString());
  }

  function testNoGroupsAdded2()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test GROUP BY t0 \n%group%", $this->conn);

    $this->assertEquals("SELECT * FROM test GROUP BY t0",
        $sql->toString());
  }

  function testAddGroupBy()
  {
    $sql = new lmbSelectRawQuery('SELECT * FROM test %group%', $this->conn);

    $sql->addGroupBy('t1');
    $sql->addGroupBy('t2');

    $this->assertEquals("SELECT * FROM test GROUP BY 't1','t2'",
        $sql->toString());
  }

  function testAddGroupByWithGroupByClause()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test GROUP BY \n%group%", $this->conn);

    $sql->addGroupBy('t1');
    $sql->addGroupBy('t2');

    $this->assertEquals("SELECT * FROM test GROUP BY \n't1','t2'",
        $sql->toString());
  }

  function testAddGroupByWithGroupByClause2()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test GROUP BY t0 \n%group%", $this->conn);

    $sql->addGroupBy('t1');
    $sql->addGroupBy('t2');

    $this->assertEquals("SELECT * FROM test GROUP BY t0 \n,'t1','t2'",
        $sql->toString());
  }

  function testAddHavingNoGroupBy()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test %having%", $this->conn);

    $sql->addHaving(new lmbSQLRawCriteria('c1=:c1:'));

    try
    {
      $sql->toString();
      $this->fail();
    }
    catch(lmbException $e){
        $this->assertTrue(true);
    }
  }

  function testAddHaving()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test GROUP BY id %having%", $this->conn);

    $sql->addHaving(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addHaving(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEquals("SELECT * FROM test GROUP BY id HAVING c1=:c1: AND c2=:c2:",
        $sql->toString());
  }

  function testAddHavingToExistingHaving()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test GROUP BY id HAVING id=1 %having%", $this->conn);

    $sql->addHaving(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addHaving(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEquals("SELECT * FROM test GROUP BY id HAVING id=1 AND c1=:c1: AND c2=:c2:",
        $sql->toString());
  }

  function testAddHavingWithExistingOrder()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test GROUP BY id HAVING id=1 %having% ORDER BY id", $this->conn);

    $sql->addHaving(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addHaving(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEquals("SELECT * FROM test GROUP BY id HAVING id=1 AND c1=:c1: AND c2=:c2: ORDER BY id",
        $sql->toString());
  }

  function testGetStatement()
  {
    $conn = $this->createMock(lmbDbConnectionInterface::class);
    $stmt = $this->createMock(lmbDbStatementInterface::class);

    $conn
        ->expects($this->once())
        ->method('newStatement');
    $stmt
        ->expects($this->once())
        ->method('set')
        ->with(array('p0t_id', 5));
    $conn
        ->method('newStatement')
        ->with($stmt);

    $sql = new lmbSelectRawQuery('SELECT * FROM test %where%', $conn);
    $sql->addCriteria(new lmbSQLFieldCriteria('t.id', 5));

    $sql->getStatement();
  }

  function testChaining()
  {
    $sql = new lmbSelectRawQuery($this->conn);
    $string = $sql->from('test')->
              from('test', 'test2')->
              field('foo', 'f')->
              join('test', 'id', 'test2', 'id')->
              order('id', 'desc')->
              group('id')->
              having('id=1')->
              where('id=2')->
              toString();

   $this->assertEquals($string,
                      "SELECT 'foo' as 'f' FROM 'test','test' 'test2' " .
                      "LEFT JOIN 'test' ON 'test.id'='test2.id' " .
                      "WHERE id=2 GROUP BY 'id' HAVING id=1 ORDER BY 'id' desc");
  }

  function testQueryWithoutWhere()
  {
    $sql = new lmbSelectRawQuery('SELECT 2', lmbToolkit::instance()->getDefaultDbConnection());
    $rs = $sql->fetch();
    $this->assertEquals(1, $rs->count());
  }

  function testQueryWithoutWhereUsingDefaultConnection()
  {
    $sql = new lmbSelectRawQuery('SELECT 1=1');
    $rs = $sql->fetch();
    $this->assertEquals(1, $rs->count());
  }

  function testQueryWithLimit()
  {
    $sql = new lmbSelectRawQuery('SELECT %fields% FROM %tables% %left_join% %where% %group% %having% %order% LIMIT 10',
                                 $this->conn);

    $string = $sql->addTable('test_db_table')->field('id')->where('id=2')->toString();
    $this->assertEquals("SELECT 'id' FROM 'test_db_table'  WHERE id=2    LIMIT 10", $string);
  }
  
  function testThrowExceptionOnActionWIthNotExistingPlaholder()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test ", $this->conn);

    $sql->addTable('test2');
    $sql->addTable('test3');

    try
    {
      $sql->toString();
      $this->fail('An exception should be thrown');
    }
    catch(lmbException $e)
    {
      $this->assertTrue(true);
    }
  }
  
}
