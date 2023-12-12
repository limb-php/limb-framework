<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\active_record\cases;

use limb\active_record\src\lmbActiveRecord;
use Tests\active_record\cases\src\ExtraForAggregateTest;
use Tests\active_record\cases\src\ImageForAggregateTest;
use Tests\active_record\cases\src\LazyMemberForTest;
use Tests\active_record\cases\src\MemberForTest;
use Tests\active_record\cases\src\NameForAggregateTest;
use Tests\active_record\cases\src\PhotoForTest;

require_once '.setup.php';

class lmbARAggregatedObjectTest extends lmbARBaseTestCase
{
    protected $tables_to_cleanup = array('member_for_test', 'photo_for_test');

    function testNewObjectReturnsEmptyAggrigatedObject()
    {
        $member = new MemberForTest();
        $this->assertInstanceOf(NameForAggregateTest::class, $member->getName());

        $this->assertNull($member->getName()->getFirst());
        $this->assertNull($member->getName()->getLast());
    }

    function testSaveLoadAggrigatedObject()
    {
        $name = new NameForAggregateTest();
        $name->setFirst($first = 'first_name');
        $name->setLast($last = 'last_name');

        $member = new MemberForTest();
        $member->setName($name);
        $member->save();

        $member2 = lmbActiveRecord:: findById(MemberForTest::class, $member->getId());
        $this->assertEquals($member2->getName()->getFirst(), $first);
        $this->assertEquals($member2->getName()->getLast(), $last);
    }

    function testSaveLoadAggrigatedObjectWithShortDefinition()
    {
        $extra = new ExtraForAggregateTest();
        $extra->setExtra('value');

        $photo = new PhotoForTest();
        $photo->setExtra($extra);
        $photo->save();

        $photo2 = lmbActiveRecord::findById(PhotoForTest::class, $photo->getId());
        $this->assertInstanceOf(ExtraForAggregateTest::class, $photo2->getExtra());
        $this->assertEquals('value_as_extra_value', $photo2->getExtra()->getValue());
    }

    function testUsingSetupMethodOnAggregatedObjectLoad()
    {
        $name = new NameForAggregateTest();
        $name->setFirst($first = 'first_name');
        $name->setLast($last = 'last_name');

        $member = new MemberForTest();
        $member->setName($name);
        $member->save();

        $member2 = lmbActiveRecord:: findById(MemberForTest::class, $member->getId());
        $member2->getName();
        $this->assertEquals($member2->saved_full_name, $name->getFull());
    }

    function testSetDirtinessOfAggregatedObjectFieldsOnSave()
    {
        $name = new NameForAggregateTest();
        $name->setLast($last = 'last_name');

        $member = new MemberForTest();
        $member->setName($name);
        $member->save();

        $name->setLast($other_last = 'other_last_name');
        $member->save();

        $member2 = lmbActiveRecord:: findById(MemberForTest::class, $member->getId());
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

        $member = new MemberForTest();
        $member->setName($name);
        $member->save();

        $this->assertEquals($member->get('name')->getFirst(), $first);
        $this->assertEquals($member->get('name')->getLast(), $last);
    }

    function testLazyAggregatedObjects()
    {
        $name = new NameForAggregateTest();
        $name->setFirst($first = 'first_name');
        $name->setLast($last = 'last_name');

        $member = new MemberForTest();
        $member->setName($name);
        $member->save();

        $member2 = new LazyMemberForTest($member->getId());

        $this->assertEquals($member->getName()->getFirst(), $first);
        $this->assertEquals($member->getName()->getLast(), $last);
    }

    function testAggregatedObjectAreImportedProperly()
    {
        $name = new NameForAggregateTest();
        $name->setFirst($first = 'first_name');
        $name->setLast($last = 'last_name');

        $member = new MemberForTest();
        $member->setName($name);
        $member->save();

        $member2 = new MemberForTest($member->export());

        $this->assertEquals($member->getName()->getFirst(), $first);
        $this->assertEquals($member->getName()->getLast(), $last);
    }
}
