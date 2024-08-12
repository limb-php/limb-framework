<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\active_record\cases;

use limb\active_record\src\lmbActiveRecord;
use limb\active_record\src\lmbARException;
use tests\active_record\cases\src\ExtraForAggregateTestObject;
use tests\active_record\cases\src\ImageForAggregateTest;
use tests\active_record\cases\src\LazyMemberForTestObject;
use tests\active_record\cases\src\MemberForTestObject;
use tests\active_record\cases\src\NameForAggregateTest;
use tests\active_record\cases\src\PhotoForTest;

require_once (dirname(__FILE__) . '/init.inc.php');

class lmbARAggregatedObjectTest extends lmbARBaseTestCase
{
    protected $tables_to_cleanup = array('member_for_test', 'photo_for_test');

    function testNewObjectReturnsEmptyAggrigatedObject()
    {
        $member = new MemberForTestObject();
        $this->assertInstanceOf(NameForAggregateTest::class, $member->getName());

        $this->assertNull($member->getName()->getFirst());
        $this->assertNull($member->getName()->getLast());
    }

    function testSaveLoadAggrigatedObject()
    {
        $name = new NameForAggregateTest();
        $name->setFirst($first = 'first_name');
        $name->setLast($last = 'last_name');

        $member = new MemberForTestObject();
        $member->setName($name);
        $member->save();

        $member2 = lmbActiveRecord::findById(MemberForTestObject::class, $member->getId());
        $this->assertEquals($member2->getName()->getFirst(), $first);
        $this->assertEquals($member2->getName()->getLast(), $last);
    }

    function testSaveLoadAggrigatedObjectWithShortDefinition()
    {
        $extra = new ExtraForAggregateTestObject();
        $extra->setExtra('value');

        $photo = new PhotoForTest();
        $photo->setExtra($extra);
        $photo->save();

        $photo2 = lmbActiveRecord::findById(PhotoForTest::class, $photo->getId());
        $this->assertInstanceOf(ExtraForAggregateTestObject::class, $photo2->getExtra());
        $this->assertEquals('value_as_extra_value', $photo2->getExtra()->getValue());
    }

    function testUsingSetupMethodOnAggregatedObjectLoad()
    {
        $name = new NameForAggregateTest();
        $name->setFirst($first = 'first_name');
        $name->setLast($last = 'last_name');

        $member = new MemberForTestObject();
        $member->setName($name);
        $member->save();

        $member2 = lmbActiveRecord::findById(MemberForTestObject::class, $member->getId());
        $member2->getName();
        $this->assertEquals($member2->saved_full_name, $name->getFull());
    }

    function testSetDirtinessOfAggregatedObjectFieldsOnSave()
    {
        $name = new NameForAggregateTest();
        $name->setLast($last = 'last_name');

        $member = new MemberForTestObject();
        $member->setName($name);
        $member->save();

        $name->setLast($other_last = 'other_last_name');
        $member->save();

        $member2 = lmbActiveRecord::findById(MemberForTestObject::class, $member->getId());
        $this->assertEquals($member2->getName()->getLast(), $other_last);
    }

    function testDoNotSettingARPrimaryKeyOnAggregatedObjects()
    {
        $image = new ImageForAggregateTest();
        $image->setExtension($extension = 'jpg');

        $photo = new PhotoForTest();
        $photo->setImage($image);

        $photo->save();
        $this->assertNotEquals($photo->getImage()->getPhotoId(), $photo->getId());

        $photo2 = lmbActiveRecord::findById(PhotoForTest::class, $photo->getId());
        $this->assertEquals($photo2->getImage()->getPhotoId(), $photo2->getId());

        $photo2->getImage()->setExtension($other_extension = 'png');
        $photo2->getImage()->setPhotoId($other_photo_id = ($photo2->getId() + 10)); // we try set AR primary key
        $photo2->save();

        $photo3 = lmbActiveRecord::findById(PhotoForTest::class, $photo2->getId());
        $this->assertEquals($photo3->getImage()->getExtension(), $other_extension);

        $this->assertNotEquals($photo3->getImage()->getPhotoId(), $other_photo_id); // affect setting AR primary key
        $this->assertEquals($photo3->getImage()->getPhotoId(), $photo3->getId()); // AR primary key not updated
    }

    function testGenericGetReturnsAlreadyExistingObject()
    {
        $name = new NameForAggregateTest();
        $name->setFirst($first = 'first_name');
        $name->setLast($last = 'last_name');

        $member = new MemberForTestObject();
        $member->setName($name);
        $member->save();

        $this->assertEquals($member->get('name')->getFirst(), $first);
        $this->assertEquals($member->get('name')->getLast(), $last);
    }

    function testWrongSetupMethod()
    {
        $name = new NameForAggregateTest();
        $name->setFirst($first = 'first_name');
        $name->setLast($last = 'last_name');

        try {
            $member = new MemberForTestObject();
            $member->setNewName($name);

            $new_name = $member->new_name->getFirst();

            $this->markTestIncomplete();
        }
        catch (lmbARException $e) {
            $this->assertTrue(true);
        }
    }

    function testLazyAggregatedObjects()
    {
        $name = new NameForAggregateTest();
        $name->setFirst($first = 'first_name');
        $name->setLast($last = 'last_name');

        $member = new MemberForTestObject();
        $member->setName($name);
        $member->save();

        $member2 = new LazyMemberForTestObject($member->getId());

        $this->assertEquals($member->getName()->getFirst(), $first);
        $this->assertEquals($member->getName()->getLast(), $last);
    }

    function testAggregatedObjectAreImportedProperly()
    {
        $name = new NameForAggregateTest();
        $name->setFirst($first = 'first_name');
        $name->setLast($last = 'last_name');

        $member = new MemberForTestObject();
        $member->setName($name);
        $member->save();

        $member2 = new MemberForTestObject($member->export());

        $this->assertEquals($member->getName()->getFirst(), $first);
        $this->assertEquals($member->getName()->getLast(), $last);
    }
}
