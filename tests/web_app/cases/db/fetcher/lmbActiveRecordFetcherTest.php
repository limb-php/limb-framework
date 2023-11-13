<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace Tests\web_app\cases\db\fetcher;

require dirname(__FILE__) . '/../.setup.php';

use limb\active_record\src\lmbActiveRecord;
use limb\web_app\src\fetcher\lmbActiveRecordFetcher;
use limb\core\src\lmbSet;
use limb\core\src\lmbCollection;
use limb\core\src\exception\lmbException;
use Tests\web_app\cases\lmbWebAppTestCase;
use Tests\active_record\cases\src\CourseForTestObject;

class CourseForFetcherTestVersionObject extends CourseForTestObject
{
  static function findSpecial()
  {
    return new lmbCollection(array(array('special' => 1)));
  }

  static function findSpecialById($id)
  {
    return new lmbSet(array('id' => $id));
  }

  static function findWithParams($param1, $param2)
  {
    return new lmbCollection(array(array('param' => $param1),
                                     array('param' => $param2)));
  }
}

class lmbActiveRecordFetcherTest extends lmbWebAppTestCase
{
  function setUp(): void
  {
    $this->_cleanUp();
  }

  function tearDown(): void
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    lmbActiveRecord::delete(CourseForTestObject::class);
  }

  function _createCourse()
  {
    $course = new CourseForTestObject();
    $course->setTitle('General Course');
    $course->save();

    return $course;
  }

  function testThrowExceptionIfClassPathNotDefined()
  {
    $fetcher = new lmbActiveRecordFetcher();
    try
    {
      $fetcher->fetch();
      $this->fail();
    }
    catch(lmbException $e){
        $this->assertTrue(true);
    }
  }

  function testFetchAllObjectsIfNoParams()
  {
    $course1 = $this->_createCourse();
    $course2 = $this->_createCourse();

    $fetcher = new lmbActiveRecordFetcher();
    $fetcher->setClassName(CourseForTestObject::class);

    $rs = $fetcher->fetch();
    $rs->rewind();
    $this->assertTrue($rs->valid());
    $this->assertEquals($rs->current()->getTitle(), $course1->getTitle());
    $rs->next();
    $this->assertTrue($rs->valid());
    $this->assertEquals($rs->current()->getTitle(), $course2->getTitle());
  }

  function testFetchWithSpecifiedFindMethod()
  {
    $fetcher = new lmbActiveRecordFetcher();
    $fetcher->setClassName(CourseForFetcherTestVersionObject::class);
    $fetcher->setFind('special');
    $rs = $fetcher->fetch();
    $rs->rewind();
    $this->assertTrue($rs->valid());
    $this->assertEquals(1, $rs->current()->get('special'));
  }

  function testFetchWithStaticFindWithParams()
  {
    $fetcher = new lmbActiveRecordFetcher();
    $fetcher->setClassName(CourseForFetcherTestVersionObject::class);
    $fetcher->setFind('with_params');
    $fetcher->addFindParam('Value1');
    $fetcher->addFindParam('Value2');
    $rs = $fetcher->fetch();
    $rs->rewind();
    $this->assertTrue($rs->valid());
    $this->assertEquals('Value1', $rs->current()->get('param'));
    $rs->next();
    $this->assertEquals('Value2', $rs->current()->get('param'));
  }

  function testFetchSingleIfFetchWithIdNotDefined()
  {
    $course1 = $this->_createCourse();
    $course2 = $this->_createCourse();

    $fetcher = new lmbActiveRecordFetcher();
    $fetcher->setClassName(CourseForTestObject::class);
    $fetcher->setRecordId($course1->getId());

    $rs = $fetcher->fetch();
    $rs->rewind();
    $this->assertTrue($rs->valid());
    $this->assertEquals($rs->current()->get('id'), $course1->getId());
    $this->assertEquals($rs->current()->get('title'), $course1->getTitle());
    $rs->next();
    $this->assertFalse($rs->valid());
  }

  function testFetchSingleWithCustomFinder()
  {
    $course1 = $this->_createCourse();

    $fetcher = new lmbActiveRecordFetcher();
    $fetcher->setClassName(CourseForFetcherTestVersionObject::class);
    $fetcher->setFind('special_by_id');
    $fetcher->setRecordId($course1->getId());

    $rs = $fetcher->fetch();
    $rs->rewind();
    $this->assertTrue($rs->valid());
    $this->assertEquals($rs->current()->get('id'), $course1->getId());
    $rs->next();
    $this->assertFalse($rs->valid());
  }

  function testFetchSingleReturnsNothingIfNoId()
  {
    $course1 = $this->_createCourse();
    $course2 = $this->_createCourse();

    $fetcher = new lmbActiveRecordFetcher();
    $fetcher->setClassName(CourseForTestObject::class);
    $fetcher->setRecordId('');

    $rs = $fetcher->fetch();
    $rs->rewind();
    $this->assertFalse($rs->valid());
  }

  function testFetchByIdsReturnsNothingIfNoIds()
  {
    $course1 = $this->_createCourse();
    $course2 = $this->_createCourse();
    $course3 = $this->_createCourse();

    $fetcher = new lmbActiveRecordFetcher();
    $fetcher->setClassName(CourseForTestObject::class);
    $fetcher->setRecordIds(null);

    $rs = $fetcher->fetch();
    $rs->rewind();
    $this->assertFalse($rs->valid());
  }

  function testFetchByIds()
  {
    $course1 = $this->_createCourse();
    $course2 = $this->_createCourse();
    $course3 = $this->_createCourse();

    $fetcher = new lmbActiveRecordFetcher();
    $fetcher->setClassName(CourseForTestObject::class);
    $fetcher->setRecordIds(array($course1->getId(), $course3->getId()));

    $rs = $fetcher->fetch();
    $rs->rewind();
    $this->assertTrue($rs->valid());
    $this->assertEquals($rs->current()->get('id'), $course1->getId());
    $rs->next();
    $this->assertTrue($rs->valid());
    $this->assertEquals($rs->current()->get('id'), $course3->getId());
    $rs->next();
    $this->assertFalse($rs->valid());
  }
}
