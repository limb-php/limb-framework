<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\nondriver\criteria;

require_once(dirname(__FILE__) . '/../init.inc.php');

use PHPUnit\Framework\TestCase;
use limb\dbal\src\criteria\lmbSQLFieldCriteria;
use limb\dbal\src\criteria\lmbSQLCriteria;
use limb\dbal\src\criteria\lmbSQLFieldBetweenCriteria;
use limb\dbal\src\exception\lmbDbException;

class lmbSQLCriteriaTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        include (dirname(__FILE__) . '/../.setup.php');
    }

    function testBuildCriteriaFromString()
    {
        $c = new lmbSQLCriteria('a=1');

        $this->assertEquals('a=1', $c->toStatementString());
    }

    function testBuildCriteriaFromStringWithNamedValues()
    {
        $c = new lmbSQLCriteria('a=:id:', array('id' => 2));

        $this->assertEquals('a=:id:', $c->toStatementString($values));
        $this->assertEquals(array('id' => 2), $values);
    }

    function testBuildCriteriaFromStringWithNonNamedValues()
    {
        $c = new lmbSQLCriteria('a=? OR b=?', array(2, 3));

        $str = $c->toStatementString($values);

        $key1 = key($values);
        next($values);
        $key2 = key($values);

        $this->assertEquals($str, 'a=:' . $key1 . ': OR b=:' . $key2 . ':');

        $this->assertEquals(2, $values[$key1]);
        $this->assertEquals(3, $values[$key2]);
    }

    function testAndChaining()
    {
        $a = new lmbSQLCriteria('a');
        $b = new lmbSQLCriteria('b');
        $a->addAnd($b);
        $this->assertEquals('a AND b', $a->toStatementString());
    }

    function testAndChainingViaAdd()
    {
        $a = new lmbSQLCriteria('a');
        $b = new lmbSQLCriteria('b');
        $a->add($b);
        $this->assertEquals('a AND b', $a->toStatementString());
    }

    function testOrChaining()
    {
        $a = new lmbSQLCriteria('a');
        $b = new lmbSQLCriteria('b');
        $a->addOr($b);
        $this->assertEquals('a OR b', $a->toStatementString());
    }

    function testComplexCriteriaIsSurroundedWithParenthesis()
    {
        $a = new lmbSQLCriteria('a');
        $b = new lmbSQLCriteria('b');
        $c = new lmbSQLCriteria('c');
        $a->add($b->add($c));
        $this->assertEquals('a AND (b AND c)', $a->toStatementString());
    }

    function testComplexChaining()
    {
        $a = new lmbSQLCriteria('a');
        $b = new lmbSQLCriteria('b');
        $c = new lmbSQLCriteria('c');
        $d = new lmbSQLCriteria('d');
        $e = new lmbSQLCriteria('e');
        $g = new lmbSQLCriteria('g');
        $h = new lmbSQLCriteria('h');
        $a->addOr($b->addAnd($c))->addAnd($d->addOr($e)->addAnd($g->addOr($h)));
        $this->assertEquals('a OR (b AND c) AND (d OR e AND (g OR h))', $a->toStatementString());
    }

    function testCreate()
    {
        $criteria = lmbSQLCriteria::create('2 = 2');
        $this->assertEquals('2 = 2', $criteria->toStatementString());
    }

    function testEmptyCriteriaChainingIsSafe()
    {
        $c = lmbSQLCriteria::create()->add(new lmbSQLCriteria());
        $this->assertEquals('1 = 1 AND 1 = 1', $c->toStatementString());
    }

    function testNot()
    {
        $a = new lmbSQLCriteria('a');
        $a->not();
        $this->assertEquals('NOT(a)', $a->toStatementString());
    }

    function testToggleNot()
    {
        $a = new lmbSQLCriteria('a');
        $a->not()->not();
        $this->assertEquals('a', $a->toStatementString());
    }

    function testNotWithChaining()
    {
        $a = new lmbSQLCriteria('a');
        $b = new lmbSQLCriteria('b');
        $a->not()->add($b);
        $this->assertEquals('NOT(a) AND b', $a->toStatementString());
    }

    function testNotAll()
    {
        $a = new lmbSQLCriteria('a');
        $a->notAll();
        $this->assertEquals('NOT(a)', $a->toStatementString());
    }

    function testToggleNotAll()
    {
        $a = new lmbSQLCriteria('a');
        $a->notAll()->notAll();
        $this->assertEquals('a', $a->toStatementString());
    }

    function testNotAllWithChaining()
    {
        $a = new lmbSQLCriteria('a');
        $b = new lmbSQLCriteria('b');
        $a->notAll()->add($b);
        $this->assertEquals('NOT(a AND b)', $a->toStatementString());
    }

    function testNotAllAndNotAtTheSameTime()
    {
        $a = new lmbSQLCriteria('a');
        $b = new lmbSQLCriteria('b');
        $a->notAll()->not()->add($b);
        $this->assertEquals('NOT(NOT(a) AND b)', $a->toStatementString());
    }

    function testObjectifyString()
    {
        $criteria = lmbSQLCriteria::objectify("id = 1");
        $this->assertEquals("id = 1", $criteria->toStatementString());
    }

    function testObjectifyObject()
    {
        $criteria = lmbSQLCriteria::objectify(new lmbSQLCriteria("id = 1"));
        $this->assertEquals("id = 1", $criteria->toStatementString());
    }

    function testObjectifyNull()
    {
        $criteria = lmbSQLCriteria::objectify(null);
        $this->assertEquals("1 = 1", $criteria->toStatementString());
    }

    function testObjectifyNotSupportedArrayFormatException()
    {
        try {
            //actually this format could be useful as well...
            $criteria = lmbSQLCriteria::objectify(array('id' => 1));
            $this->fail();
        } catch (lmbDbException $e) {
            $this->assertTrue(true);
        }
    }

    function testPassRawCriteriaToConstructor()
    {
        $criteria = new lmbSQLCriteria('2 = 2');
        $this->assertEquals('2 = 2', $criteria->toStatementString());
    }

    function testBetween()
    {
        $criteria = lmbSQLCriteria::between('id', 1, 100);
        $this->assertEquals($criteria, new lmbSQLFieldBetweenCriteria('id', 1, 100));
    }

    function testIn()
    {
        $criteria = lmbSQLCriteria::in('id', array(1, 2));
        $this->assertEquals($criteria, new lmbSQLFieldCriteria('id', array(1, 2), lmbSQLFieldCriteria::IN));
    }

    function testInWithArrayProcessor()
    {
        $criteria = lmbSQLCriteria::in('id', array("10foo", "20bar"), 'intval');
        $this->assertEquals($criteria, new lmbSQLFieldCriteria('id', array(10, 20), lmbSQLFieldCriteria::IN));
    }

    function testEqual()
    {
        $criteria = lmbSQLCriteria::equal('id', 1);
        $this->assertEquals($criteria, new lmbSQLFieldCriteria('id', 1, lmbSQLFieldCriteria::EQUAL));
    }

    function testLike()
    {
        $criteria = lmbSQLCriteria::like('id', '%foo%');
        $this->assertEquals($criteria, new lmbSQLFieldCriteria('id', '%foo%', lmbSQLFieldCriteria::LIKE));
    }

    function testIsNull()
    {
        $criteria = lmbSQLCriteria::isNull('id');
        $this->assertEquals($criteria, new lmbSQLFieldCriteria('id', null, lmbSQLFieldCriteria::IS_NULL));
    }

    function testGreater()
    {
        $criteria = lmbSQLCriteria::greater('id', 11);
        $this->assertEquals($criteria, new lmbSQLFieldCriteria('id', 11, lmbSQLFieldCriteria::GREATER));
    }

    function testLess()
    {
        $criteria = lmbSQLCriteria::less('id', 12);
        $this->assertEquals($criteria, new lmbSQLFieldCriteria('id', 12, lmbSQLFieldCriteria::LESS));
    }
}
