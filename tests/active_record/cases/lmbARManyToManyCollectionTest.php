<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\active_record\cases;

require_once '.setup.php';

use limb\active_record\src\lmbARManyToManyCollection;
use limb\active_record\src\lmbActiveRecord;
use limb\core\src\exception\lmbException;
use limb\dbal\src\lmbDBAL;
use limb\dbal\src\lmbTableGateway;
use tests\active_record\cases\src\GroupForTestObject;
use tests\active_record\cases\src\GroupForTestObjectStub;
use tests\active_record\cases\src\UserForTestObject;
use tests\active_record\cases\src\UserForTestWithSpecialRelationTable;

class lmbARManyToManyCollectionTest extends lmbARBaseTestCase
{
  protected $tables_to_cleanup = array('group_for_test', 'user_for_test', 'user_for_test2group_for_test', 'extended_user_for_test2group_for_test'); 

  function testAddToWithExistingOwner()
  {
    $user = $this->_createUserAndSave();

    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();

    $collection = new lmbARManyToManyCollection('groups', $user);
    $collection->add($group1);
    $collection->add($group2);

    $arr = $collection->getArray();

    $this->assertEquals($arr[0]->getTitle(), $group1->getTitle());
    $this->assertEquals($arr[1]->getTitle(), $group2->getTitle());
    $this->assertEquals(sizeof($arr), 2);

    $collection2 = new lmbARManyToManyCollection('groups', $user);
    $arr = $collection2->getArray();

    $this->assertEquals($arr[0]->getTitle(), $group1->getTitle());
    $this->assertEquals($arr[1]->getTitle(), $group2->getTitle());
    $this->assertEquals(sizeof($arr), 2);
  }

  function testAddToWithNonSavedOwner()
  {
    $user = $this->_initUser();

    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();

    $collection = new lmbARManyToManyCollection('groups', $user);
    $collection->add($group1);
    $collection->add($group2);

    $arr = $collection->getArray();
    $this->assertEquals(sizeof($arr), 2);
    $this->assertEquals($arr[0]->getTitle(), $group1->getTitle());
    $this->assertEquals($arr[1]->getTitle(), $group2->getTitle());

    $collection2 = new lmbARManyToManyCollection('groups', $user);
    $arr = $collection2->getArray();

    $this->assertEquals(sizeof($arr), 0);
  }

  function testSaveWithExistingOwnerDoesNothing()
  {
    $group1 = $this->createMock(GroupForTestObject::class);
    $group2 = $this->createMock(GroupForTestObject::class);

    $user = $this->_createUserAndSave();

    $collection = new lmbARManyToManyCollection('groups', $user);

    $collection->add($group1);
    $collection->add($group2);

    $group1->expects($this->never())->method('save');
    $group2->expects($this->never())->method('save');

    $collection->save();
  }

  function testSaveWithNonSavedOwner()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();

    $user = $this->_initUser();

    $collection = new lmbARManyToManyCollection('groups', $user);
    $collection->add($group1);
    $collection->add($group2);

    $collection2 = new lmbARManyToManyCollection('groups', $user);
    $this->assertEquals(sizeof($collection2->getArray()), 0);

    $user->save();
    $collection->save();

    $collection3 = new lmbARManyToManyCollection('groups', $user);
    $arr = $collection3->getArray();
    $this->assertEquals(sizeof($arr), 2);
    $this->assertEquals($arr[0]->getTitle(), $group1->getTitle());
    $this->assertEquals($arr[1]->getTitle(), $group2->getTitle());
  }

  function testSavingOwnerDoesntAffectCollection()
  {
    $group1 = new GroupForTestObjectStub();
    $group1->setTitle('Group1');
    $group2 = new GroupForTestObjectStub();
    $group2->setTitle('Group2');

    $user = $this->_initUser();

    $collection = new lmbARManyToManyCollection('groups', $user);
    $collection->add($group1);

    $user->save();

    $collection->add($group2);

    //items in memory
    $arr = $collection->getArray();
    $this->assertEquals(sizeof($arr), 2);
    $this->assertEquals($arr[0]->getTitle(), $group1->getTitle());
    $this->assertEquals($arr[1]->getTitle(), $group2->getTitle());
    $this->assertEquals($group1->save_calls, 0);
    $this->assertEquals($group2->save_calls, 0);

    //...and not db yet
    $collection2 = new lmbARManyToManyCollection('groups', $user);
    $this->assertEquals(sizeof($collection2->getArray()), 0);

    $collection->save();

    $collection3 = new lmbARManyToManyCollection('groups', $user);
    $arr = $collection3->getArray();
    $this->assertEquals(sizeof($arr), 2);
    $this->assertEquals($arr[0]->getTitle(), $group1->getTitle());
    $this->assertEquals($arr[1]->getTitle(), $group2->getTitle());

    //check items not saved twice
    $collection->save();

    $this->assertEquals($group1->save_calls, 1);
    $this->assertEquals($group2->save_calls, 1);

    $collection4 = new lmbARManyToManyCollection('groups', $user);
    $arr = $collection4->getArray();
    $this->assertEquals(sizeof($arr), 2);
    $this->assertEquals($arr[0]->getTitle(), $group1->getTitle());
    $this->assertEquals($arr[1]->getTitle(), $group2->getTitle());
  }

  function testLoadOnlyProperRecordsWithExistingOwner()
  {
    $g1 = $this->_initGroup();
    $g2 = $this->_initGroup();

    $user1 = $this->_createUserAndSave(array($g1, $g2));

    $g3 = $this->_initGroup();
    $g4 = $this->_initGroup();

    $user2 = $this->_createUserAndSave(array($g3, $g4));

    $collection1 = new lmbARManyToManyCollection('groups', $user1);
    $this->assertEquals($collection1->count(), 2);
    $arr = $collection1->getArray();
    $this->assertEquals(sizeof($arr), 2);
    $this->assertEquals($arr[0]->getTitle(), $g1->getTitle());
    $this->assertEquals($arr[1]->getTitle(), $g2->getTitle());

    $collection2 = new lmbARManyToManyCollection('groups', $user2);
    $this->assertEquals($collection2->count(), 2);
    $arr = $collection2->getArray();
    $this->assertEquals(sizeof($arr), 2);
    $this->assertEquals($arr[0]->getTitle(), $g3->getTitle());
    $this->assertEquals($arr[1]->getTitle(), $g4->getTitle());
  }

  function testCountWithExistingOwner()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();

    $user = $this->_createUserAndSave();

    $collection = new lmbARManyToManyCollection('groups', $user);
    $this->assertEquals($collection->count(), 0);
    $collection->add($group1);
    $collection->add($group2);

    $this->assertEquals($collection->count(), 2);
  }

  function testCountWithNonSavedOwner()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();

    $user = new UserForTestObject();

    $collection = new lmbARManyToManyCollection('groups', $user);
    $this->assertEquals($collection->count(), 0);

    $collection->add($group1);
    $collection->add($group2);

    $this->assertEquals($collection->count(), 2);
  }

  function testImplementsCountable()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();

    $user = $this->_createUserAndSave();

    $collection = new lmbARManyToManyCollection('groups', $user);
    $this->assertEquals(sizeof($collection), 0);

    $collection->add($group1);
    $collection->add($group2);

    $this->assertEquals(sizeof($collection), 2);
  }

  function testPartiallyImplementsArrayAccess()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();

    $user = $this->_createUserAndSave();

    $collection = new lmbARManyToManyCollection('groups', $user);

    $collection[] = $group1;
    $collection[] = $group2;

    $this->assertEquals($collection[0]->getId(), $group1->getId());
    $this->assertEquals($collection[1]->getId(), $group2->getId());
    $this->assertNull($collection[2]);

    $this->assertTrue(isset($collection[0]));
    $this->assertTrue(isset($collection[1]));
    $this->assertFalse(isset($collection[2]));

    //we can't really implement just every php array use case
    $this->assertNull($collection['foo']);
    $this->assertFalse(isset($collection['foo']));
    $collection[3] = 'foo';
    $this->assertNull($collection[3]);
  }

  function testRemoveAllWithExistingOwner()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();

    $user = $this->_createUserAndSave(array($group1, $group2));

    $collection = new lmbARManyToManyCollection('groups', $user);
    $collection->removeAll();

    $user2 = lmbActiveRecord :: findById(UserForTestObject::class, $user->getId());

    $collection = new lmbARManyToManyCollection('groups', $user2);
    $this->assertEquals(sizeof($collection->getArray()), 0);
  }

  function testRemoveAllWithNonSavedOwner()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();

    $user = $this->_initUser();

    $collection = new lmbARManyToManyCollection('groups', $user);
    $collection->add($group1);
    $collection->add($group2);
    $collection->removeAll();

    $this->assertEquals($collection->count(), 0);
  }

  function testRemoveAllDeletesOnlyProperRecordsFromTable()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();

    $user = new UserForTestWithSpecialRelationTable();
    $user->setFirstName('User' . mt_rand());
    $user->save();

    $collection = new lmbARManyToManyCollection('groups', $user);
    $collection->add($group1);
    $collection->add($group2);

    $db_table = new lmbTableGateway('extended_user_for_test2group_for_test');
    $db_table->insert(array('user_id' => $user->getId(),
                            'other_id' => 100));

    $collection->removeAll();

    $this->assertEquals($db_table->select()->count(), 1);
  }

  function testPaginateWithNonSavedOwner()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();
    $group3 = $this->_initGroup();

    $user = $this->_initUser();

    $collection = new lmbARManyToManyCollection('groups', $user);
    $collection->add($group1);
    $collection->add($group2);
    $collection->add($group3);

    $collection->paginate($offset = 0, $limit = 2);

    $this->assertEquals($collection->count(), 3);
    $arr = $collection->getArray();

    $this->assertEquals(sizeof($arr), 2);
    $this->assertEquals($arr[0]->getTitle(), $group1->getTitle());
    $this->assertEquals($arr[1]->getTitle(), $group2->getTitle());
  }

  function testPaginateWithExistingOwner()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();
    $group3 = $this->_initGroup();

    $user = $this->_createUserAndSave(array($group1, $group2, $group3));

    $collection = new lmbARManyToManyCollection('groups', $user);
    $collection->paginate($offset = 0, $limit = 2);

    $this->assertEquals($collection->count(), 3);
    $arr = $collection->getArray();

    $this->assertEquals(sizeof($arr), 2);
    $this->assertEquals($arr[0]->getTitle(), $group1->getTitle());
    $this->assertEquals($arr[1]->getTitle(), $group2->getTitle());
  }

  function testSortWithExistingOwner()
  {
    $group1 = new GroupForTestObject();
    $group1->setTitle('A-Group');
    $group2 = new GroupForTestObject();
    $group2->setTitle('B-Group');
    $group3 = new GroupForTestObject();
    $group3->setTitle('C-Group');

    $user = $this->_createUserAndSave(array($group1, $group2, $group3));

    $collection = new lmbARManyToManyCollection('groups', $user);
    $collection->sort(array('title' => 'DESC'));

    $this->assertEquals($collection->count(), 3);
    $arr = $collection->getArray();

    $this->assertEquals(sizeof($arr), 3);
    $this->assertEquals($arr[0]->getTitle(), $group3->getTitle());
    $this->assertEquals($arr[1]->getTitle(), $group2->getTitle());
    $this->assertEquals($arr[2]->getTitle(), $group1->getTitle());
  }

  function testSortWithNonSavedOwner()
  {
    $group1 = new GroupForTestObject();
    $group1->setTitle('A-Group');
    $group2 = new GroupForTestObject();
    $group2->setTitle('B-Group');
    $group3 = new GroupForTestObject();
    $group3->setTitle('C-Group');

    $user = $this->_initUser();

    $collection = new lmbARManyToManyCollection('groups', $user);
    $collection->add($group1);
    $collection->add($group2);
    $collection->add($group3);

    $collection->sort(array('title' => 'DESC'));
    $this->assertEquals($collection->at(0)->getTitle(), 'C-Group');
    $this->assertEquals($collection->at(1)->getTitle(), 'B-Group');
    $this->assertEquals($collection->at(2)->getTitle(), 'A-Group');
  }

  function testFindWithExistingOwner()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();
    $group3 = $this->_initGroup();

    $user = $this->_createUserAndSave(array($group1, $group2, $group3));

    $groups = $user->getGroups()->find(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("group_id") . "=" . $group1->getId());
    $this->assertEquals($groups->count(), 1);
    $this->assertEquals($groups->at(0)->getTitle(), $group1->getTitle());
  }

  function testFindWithNonSavedOwner_TODO()
  {
    $g1 = $this->_initGroup();
    $g2 = $this->_initGroup();
    $user = $this->_initUser(array($g1, $g2));

    try
    {
      $groups = $user->getGroups()->find("id=" . $g1->getId());
      $this->fail();
    }
    catch(lmbException $e){
        $this->assertTrue(true);
    }
  }

  function testFindFirstWithExistingOwner()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();
    $group3 = $this->_initGroup();

    $user = $this->_createUserAndSave(array($group1, $group2, $group3));

    $group = $user->getGroups()->findFirst(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("group_id") . "=" . $group1->getId() . " OR " . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("group_id") . "=" . $group2->getId());
    $this->assertEquals($group->getTitle(), $group1->getTitle());
  }

  function testFindFirstWithNonSavedOwner_TODO()
  {
    $g1 = $this->_initGroup();
    $g2 = $this->_initGroup();
    $user = $this->_initUser(array($g1, $g2));

    try
    {
      $group = $user->getGroups()->findFirst(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("group_id") . "=" . $g1->getId() . " OR " . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("group_id") . "=" . $g2->getId());
      $this->fail();
    }
    catch(lmbException $e){
        $this->assertTrue(true);
    }
  }

  function testAtWithExistingOwner()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();
    $group3 = $this->_initGroup();

    $user = $this->_createUserAndSave(array($group1, $group2, $group3));
    $collection = new lmbARManyToManyCollection('groups', $user);

    $this->assertEquals($collection->at(0)->getTitle(), $group1->getTitle());
    $this->assertEquals($collection->at(2)->getTitle(), $group3->getTitle());
    $this->assertEquals($collection->at(1)->getTitle(), $group2->getTitle());
  }

  function testSet()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();
    $group3 = $this->_initGroup();

    $user = $this->_createUserAndSave(array($group1, $group2, $group3));
    $collection = new lmbARManyToManyCollection('groups', $user);

    $collection->set(array($group1, $group3));

    $this->assertEquals($collection->count(), 2);
    $this->assertEquals($collection->at(0)->getTitle(), $group1->getTitle());
    $this->assertEquals($collection->at(1)->getTitle(), $group3->getTitle());
  }
  
  function testSetDontReInsertSameRecordsIfTheyExists()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();
    $group3 = $this->_initGroup();
    $group4 = $this->_initGroup();

    $user = $this->_createUserAndSave(array($group1, $group2, $group3));
    
    $table = lmbDBAL::table('user_for_test2group_for_test', $this->conn);
    $records = $table->select()->getArray();
    $this->assertEquals(count($records), 3);
    
    $collection = new lmbARManyToManyCollection('groups', $user);
    $collection->set(array($group1, $group2, $group3, $group4));

    $new_records = $table->select()->getArray();
    $this->assertEquals(count($new_records), 4);
    $this->assertEquals($records[0]['id'], $new_records[0]['id']);
    $this->assertEquals($records[1]['id'], $new_records[1]['id']);
    $this->assertEquals($records[2]['id'], $new_records[2]['id']);
    $this->assertEquals($new_records[3]['user_id'], $user->getId());
  }
  
  function testRemove_DeleteRecordAndCleanUpInternalIterator()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();
    $group3 = $this->_initGroup();

    $user = $this->_createUserAndSave(array($group1, $group2, $group3));
    $groups = $user->getGroups();
    $arr = $groups->getArray();
    $this->assertEquals(count($arr), 3);
    
    $groups->remove($group2);
    $arr = $groups->getArray();
    $this->assertEquals(count($arr), 2);
  }

  protected function _initUser($groups = array())
  {
    $user = new UserForTestObject();
    $user->setFirstName('User' . mt_rand());

    if(sizeof($groups))
    {
      foreach($groups as $group)
        $user->getGroups()->add($group);
    }

    return $user;
  }

  protected function _createUserAndSave($groups = array())
  {
    $user = $this->_initUser($groups);
    $user->save();
    return $user;
  }

  protected function _initGroup()
  {
    $group = new GroupForTestObject();
    $group->setTitle('Group' . mt_rand());
    return $group;
  }

}
