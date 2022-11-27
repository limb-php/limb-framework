<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\active_record\cases;

use limb\core\src\lmbObject;
use limb\datetime\src\lmbDateTime;
use limb\active_record\src\lmbActiveRecord;

// Aggregate must implement lmbSet interface.
class NameForAggregateTest extends lmbObject
{
  protected $first;
  protected $last;

  function getFull()
  {
    return $this->first . ' ' . $this->last;
  }
}

class MemberForTest extends lmbActiveRecord
{
  protected $_composed_of = array('name' => array('class' => 'NameForAggregateTest',
                                                  'mapping' => array('first' => 'first_name',
                                                                     'last' => 'last_name'),
                                                  'setup_method' => 'setupName'));

  public $saved_full_name = '';

  function setupName($name_object)
  {
    $this->saved_full_name = $name_object->getFull();
    return $name_object;
  }
}

class LazyMemberForTest extends MemberForTest
{
  protected $_db_table_name = 'member_for_test';

  protected $_lazy_attributes = array('name');
}

class ImageForAggregateTest extends lmbObject
{
  protected $extension;
  protected $photo_id;

  function getUrl()
  {
    return '/image_' . $this->photo_id . '.' . $this->image_extension;
  }
}

class ExtraForAggregateTest extends lmbObject
{
  protected $extra;

  function getValue()
  {
    return $this->extra . '_as_extra_value';
  }
}

class PhotoForTest extends lmbActiveRecord
{
  protected $_composed_of = array('image' => array('class' => 'ImageForAggregateTest',
                                                   'mapping' => array('photo_id' => 'id',
                                                                      'extension' => 'image_extension')),

                                  'extra' => array('class' =>'ExtraForAggregateTest'));
}

class lmbARAggregatedObjectTest extends lmbARBaseTestCase
{
  protected $tables_to_cleanup = array('member_for_test', 'photo_for_test');

  function testNewObjectReturnsEmptyAggrigatedObject()
  {
    $member = new MemberForTest();
    $this->assertIsA($member->getName(), 'NameForAggregateTest');

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

    $member2 = lmbActiveRecord :: findById('MemberForTest', $member->getId());
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

    $photo2 = lmbActiveRecord :: findById('PhotoForTest', $photo->getId());
    $this->assertIsA($photo2->getExtra(), 'ExtraForAggregateTest');
    $this->assertEquals($photo2->getExtra()->getValue(), 'value_as_extra_value');
  }

  function testUsingSetupMethodOnAggregatedObjectLoad()
  {
    $name = new NameForAggregateTest();
    $name->setFirst($first = 'first_name');
    $name->setLast($last = 'last_name');

    $member = new MemberForTest();
    $member->setName($name);
    $member->save();

    $member2 = lmbActiveRecord :: findById('MemberForTest', $member->getId());
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

    $member2 = lmbActiveRecord :: findById('MemberForTest', $member->getId());
    $this->assertEquals($member2->getName()->getLast(), $other_last);
  }

  function testDoNotSettingARPrimaryKeyOnAggregatedObjects()
  {
    $image = new ImageForAggregateTest();
    $image->setExtension($extension = 'jpg');

    $photo = new PhotoForTest();
    $photo->setImage($image);

    $photo->save();
    $this->assertNotEqual($photo->getImage()->getPhotoId(), $photo->getId());

    $photo2 = lmbActiveRecord :: findById('PhotoForTest', $photo->getId());
    $this->assertEquals($photo2->getImage()->getPhotoId(), $photo2->getId());

    $photo2->getImage()->setExtension($other_extension = 'png');
    $photo2->getImage()->setPhotoId($other_photo_id = ($photo2->getId() + 10)); // we try set AR primary key
    $photo2->save();

    $photo3 = lmbActiveRecord :: findById('PhotoForTest', $photo2->getId());
    $this->assertEquals($photo3->getImage()->getExtension(), $other_extension);

    $this->assertNotEqual($photo3->getImage()->getPhotoId(), $other_photo_id); // affect setting AR primary key
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
