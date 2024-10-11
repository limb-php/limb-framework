<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\Tests\ActiveRecord\Cases;

use limb\active_record\lmbActiveRecord;
use Limb\Tests\ActiveRecord\Cases\src\GroupForTestObject;
use Limb\Tests\ActiveRecord\Cases\src\GroupsForTestCollectionStub;
use Limb\Tests\ActiveRecord\Cases\src\UserForTestObject;
use Limb\Tests\ActiveRecord\Cases\src\UserForTestWithCustomCollection;

class lmbARManyToManyRelationsTest extends lmbARBaseTestCase
{
    protected $tables_to_cleanup = array('user_for_test', 'group_for_test', 'user_for_test2group_for_test', 'test_one_table_object');

    function testMapPropertyToField()
    {
        $group = new GroupForTestObject();
        $this->assertEquals('users', $group->mapFieldToProperty('group_id'));
        $this->assertNull($group->mapFieldToProperty('blah'));
    }

    function testNewObjectReturnsEmptyCollection()
    {
        $user = new UserForTestObject();
        $groups = $user->getGroups();
        $groups->rewind();
        $this->assertFalse($groups->valid());
    }

    function testAddFromOneSideOfRelation()
    {
        $user = $this->creator->initUser();

        $group1 = $this->creator->initGroup();
        $group2 = $this->creator->initGroup();

        $user->addToGroups($group1);
        $user->addToGroups($group2);
        $user->save();

        $user2 = lmbActiveRecord:: findById(UserForTestObject::class, $user->getId());
        $rs = $user2->getGroups();

        $rs->rewind();
        $this->assertTrue($rs->valid());
        $this->assertEquals($rs->current()->getTitle(), $group1->getTitle());
        $this->assertEquals($rs->current()->getId(), $group1->getId());
        $rs->next();
        $this->assertEquals($rs->current()->getTitle(), $group2->getTitle());
        $this->assertEquals($rs->current()->getId(), $group2->getId());
    }

    function testSetRelation()
    {
        $user1 = $this->creator->initUser();
        $user2 = $this->creator->initUser();

        $group1 = $this->creator->initGroup();
        $group2 = $this->creator->initGroup();

        $user1->addToGroups($group1);
        $user1->addToGroups($group2);
        $user2->addToGroups($group1);
        $user2->addToGroups($group2);

        $user1->save();
        $user2->save();
        $this->assertEquals(2, $user1->getGroups()->count());
        $this->assertEquals(2, $user2->getGroups()->count());

        $user1->getGroups()->set(array($group1));
        $user1->save();
        $user2->save();

        $this->assertEquals(1, $user1->getGroups()->count());
        $this->assertEquals(2, $user2->getGroups()->count());
    }

    function testLoadShouldNotMixTables()
    {
        $user1 = $this->creator->initUser();
        $user2 = $this->creator->initUser();

        $group1 = $this->creator->initGroup();
        $group2 = $this->creator->initGroup();

        $user1->addToGroups($group1);
        $user1->addToGroups($group2);
        $user1->save();

        $user2->addToGroups($group1);
        $user2->addToGroups($group2);
        $user2->save();

        $user3 = lmbActiveRecord:: findById(UserForTestObject::class, $user2->getId());
        $rs = $user3->getGroups();

        $rs->rewind();
        $this->assertTrue($rs->valid());
        $this->assertEquals($rs->current()->getTitle(), $group1->getTitle());
        $this->assertEquals($rs->current()->getId(), $group1->getId());
        $rs->next();
        $this->assertEquals($rs->current()->getTitle(), $group2->getTitle());
        $this->assertEquals($rs->current()->getId(), $group2->getId());
    }

    function testFetch_WithRelatedObjectsUsing_WithMethod()
    {
        $linked_object1 = $this->creator->createOneTableObject();
        $linked_object2 = $this->creator->createOneTableObject();

        $user1 = $this->creator->createUser($linked_object1);
        $user2 = $this->creator->createUser($linked_object2);

        $group = $this->creator->createGroup();

        $group->setUsers(array($user1, $user2));

        $group2 = lmbActiveRecord:: findById(GroupForTestObject::class, $group->getId());
        $arr = $group2->getUsers()->join('linked_object')->getArray();

        //make sure we really eager fetching
        $this->db->delete('test_one_table_object');

        $this->assertEquals($arr[0]->getFirstName(), $user1->getFirstName());
        $this->assertEquals($arr[1]->getFirstName(), $user2->getFirstName());
    }

    function testSetingCollectionDirectlyCallsAddToMethod()
    {
        $user = $this->creator->initUser();

        $g1 = $this->creator->initGroup();
        $g2 = $this->creator->initGroup();

        $user->setGroups(array($g1, $g2));
        $arr = $user->getGroups()->getArray();
        $this->assertEquals(sizeof($arr), 2);
        $this->assertEquals($arr[0]->getTitle(), $g1->getTitle());
        $this->assertEquals($arr[1]->getTitle(), $g2->getTitle());
    }

    function testSetFlushesPreviousCollection()
    {
        $user = $this->creator->initUser();

        $g1 = $this->creator->initGroup();
        $g2 = $this->creator->initGroup();

        $user->addToGroups($g1);
        $user->addToGroups($g2);

        $user->setGroups(array($g1));
        $groups = $user->getGroups()->getArray();
        $this->assertEquals($groups[0]->getTitle(), $g1->getTitle());
        $this->assertEquals(sizeof($groups), 1);
    }

    function testUpdateRelations()
    {
        $user = $this->creator->initUser();

        $group1 = $this->creator->initGroup();
        $group2 = $this->creator->initGroup();

        $user->addToGroups($group1);
        $user->addToGroups($group2);
        $user->save();

        $user2 = lmbActiveRecord::findById(UserForTestObject::class, $user->getId());
        $user2->setGroups(array($group2));
        $user2->save();

        $user3 = lmbActiveRecord::findById(UserForTestObject::class, $user->getId());
        $groups = $user3->getGroups();

        $this->assertEquals($groups->at(0)->getTitle(), $group2->getTitle());
        $this->assertEquals($groups->count(), 1);
    }

    function testDeleteAlsoRemovesManyToManyRecords()
    {
        $user1 = $this->creator->initUser();
        $user2 = $this->creator->initUser();

        $group1 = $this->creator->initGroup();
        $group2 = $this->creator->initGroup();

        $user1->addToGroups($group1);
        $user1->addToGroups($group2);
        $user1->save();

        $user2->addToGroups($group1);
        $user2->addToGroups($group2);
        $user2->save();

        $user3 = lmbActiveRecord::findById(UserForTestObject::class, $user1->getId());
        $user3->destroy();

        $this->assertEquals($this->db->count('user_for_test2group_for_test'), 2);

        $user4 = lmbActiveRecord::findById(UserForTestObject::class, $user2->getId());
        $groups = $user4->getGroups();
        $this->assertEquals($groups->at(0)->getTitle(), $group1->getTitle());
        $this->assertEquals($groups->at(1)->getTitle(), $group2->getTitle());
        $this->assertEquals($groups->count(), 2);
    }

    function testUseCustomCollection()
    {
        $user = new UserForTestWithCustomCollection();
        $this->assertTrue($user->getGroups() instanceof GroupsForTestCollectionStub);
    }

//  function testErrorListIsSharedWithCollection()
//  {
//    $user = $this->creator->initUser();
//
//    $group = new GroupForTestObject();
//
//    $validator = new lmbValidator();
//    $validator->addRequiredRule('title');
//    $group->setValidator($validator);
//
//    $user->addToGroups($group);
//
//    $error_list = new lmbErrorList();
//    $result = $user->trySave($error_list);
//
//    $this->assertFalse($result);
//    $this->assertEquals(1, $user->getGroups()->count());
//  }

    function testManyToManyRelationWithCriteria()
    {
        $user = $this->creator->initUser();

        $g1 = $this->creator->createGroup('foo');
        $g2 = $this->creator->createGroup('bar');
        $g3 = $this->creator->createGroup('condition');
        $this->assertEquals('condition', $g3->getTitle());

        $user->setGroups(array($g1, $g2, $g3));
        $user->save();
        $user = new UserForTestObject($user->id);
        $arr = $user->getCgroups()->getArray();
        $this->assertInstanceOf(GroupForTestObject::class, $arr[0]);
        $this->assertEquals(1, sizeof($arr));
        $this->assertEquals($arr[0]->getTitle(), $g3->getTitle());
    }

}
