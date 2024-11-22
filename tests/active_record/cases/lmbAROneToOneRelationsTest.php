<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\active_record\cases;

use limb\active_record\src\lmbActiveRecord;
use limb\active_record\src\lmbARNotFoundException;
use limb\validation\src\exception\lmbValidationException;
use tests\active_record\cases\src\PersonForTestNoCascadeDelete;
use tests\active_record\cases\src\PersonForTestObject;
use tests\active_record\cases\src\PersonForTestWithNotRequiredSocialSecurity;
use tests\active_record\cases\src\PersonForTestWithRequiredSocialSecurity;
use tests\active_record\cases\src\SocialSecurityForTestObject;

class lmbAROneToOneRelationsTest extends lmbARBaseTestCase
{
    protected $tables_to_cleanup = array('person_for_test', 'social_security_for_test');

    function testHas()
    {
        $person = new PersonForTestObject();
        $this->assertTrue(isset($person['social_security']));
    }

    function testNewObjectReturnsNullChild()
    {
        $person = new PersonForTestObject();
        $this->assertNull($person->getSocialSecurity());
    }

    function testNewObjectReturnsNullParent()
    {
        $number = new SocialSecurityForTestObject();
        $this->assertNull($number->getPerson());
    }

    function testSaveChild()
    {
        $person = $this->creator->initPerson();
        $number = $this->creator->initSocialSecurity();

        $person->setSocialSecurity($number);

        $this->assertNull($number->getId());

        $person->save();

        $this->assertNotNull($number->getId());
    }

    function testSaveParent()
    {
        $person = $this->creator->initPerson();
        $number = $this->creator->initSocialSecurity();

        $number->setPerson($person);

        $this->assertNull($person->getId());

        $number->save();

        $this->assertNotNull($person->getId());
    }

    function testDontSaveParentSecondTimeIfChildWasChanged()
    {
        $person = new PersonForTestObject();
        $person->setName('Jim');

        $number = new SocialSecurityForTestObject();
        $number->setCode('099123');

        $person->setSocialSecurity($number);
        $person->save();

        $this->assertEquals($person->save_count, 1);

        $person->save();

        $this->assertEquals($person->save_count, 1);
    }

    function testSavingParentSavesChildAsWell()
    {
        $person = new PersonForTestObject();
        $person->setName('Jim');

        $number = new SocialSecurityForTestObject();
        $number->setCode('099123');

        $person->setSocialSecurity($number);
        $person->save();

        $number->setCode($new_code = '0022112');
        $person->save();

        $loaded_number = new SocialSecurityForTestObject($number->getId());
        $this->assertEquals($loaded_number->getCode(), $new_code);
    }

    function testChangingChildObjectIdDirectly()
    {
        $person = $this->creator->initPerson();
        $number1 = $this->creator->initSocialSecurity();

        $person->setSocialSecurity($number1);
        $person->save();

        $number2 = $this->creator->initSocialSecurity();
        $number2->save();

        $person2 = new PersonForTestObject($person->getId());
        $this->assertEquals($person2->getSocialSecurity()->getId(), $number1->getId());

        $person2->set('ss_id', $number2->getId());
        $person2->save();

        $person3 = new PersonForTestObject($person->getId());
        $this->assertEquals($person3->getSocialSecurity()->getId(), $number2->getId());
    }

    function testChangingChildIdRelationFieldDirectlyHasNoAffectIfChildObjectPropertyIsDirty()
    {
        $person = $this->creator->initPerson();

        $number1 = $this->creator->initSocialSecurity();

        $person->setSocialSecurity($number1);
        $person->save();

        $number2 = $this->creator->initSocialSecurity();
        $number2->save();

        $person2 = new PersonForTestObject($person->getId());
        $this->assertEquals($person2->getSocialSecurity()->getId(), $number1->getId());

        $person2->set('ss_id', $number2->getId()); // changing child relation field directly
        $person2->setSocialSecurity($number1); // and making child object dirty
        $person2->save();

        $person3 = new PersonForTestObject($person->getId());
        $this->assertEquals($person3->getSocialSecurity()->getId(), $number1->getId());
    }

    function testLoadParentObject()
    {
        $person = $this->creator->initPerson();
        $number = $this->creator->initSocialSecurity();
        $person->setSocialSecurity($number);
        $person->save();

        $number2 = lmbActiveRecord:: findById(SocialSecurityForTestObject::class, $number->getId());

        $person2 = $number2->getPerson();

        $this->assertEquals($person2->getId(), $person->getId());
        $this->assertEquals($person2->getName(), $person->getName());
    }

    function testGenericGetLoadsChildObject()
    {
        $person = $this->creator->initPerson();
        $number = $this->creator->initSocialSecurity();
        $person->setSocialSecurity($number);
        $person->save();

        $number2 = lmbActiveRecord:: findById(SocialSecurityForTestObject::class, $number->getId());

        $person2 = $number2->getPerson();

        $this->assertEquals($person2->getId(), $person->getId());
        $this->assertEquals($person2->getName(), $person->getName());
    }

    function testLoadChildObject()
    {
        $person = $this->creator->initPerson();
        $number = $this->creator->initSocialSecurity();
        $person->setSocialSecurity($number);
        $person_id = $person->save();

        $person2 = lmbActiveRecord:: findById(PersonForTestObject::class, $person_id);
        $number2 = $person2->getSocialSecurity();

        $this->assertEquals($person2->getId(), $person_id);
        $this->assertEquals($person2->getName(), $person->getName());
        $this->assertEquals($number2->getId(), $number->getId());
        $this->assertEquals($number2->getCode(), $number->getCode());
    }

    function testLoadNonExistingChildObject_ThrowsExceptionByDefault()
    {
        $person = $this->creator->initPerson();
        $number = $this->creator->initSocialSecurity();
        $person->setSocialSecurity($number);
        $person->save();

        $this->db->delete('social_security_for_test', 'id = ' . $number->getId());

        $person2 = lmbActiveRecord:: findById(PersonForTestObject::class, $person->getId());

        try {
            $person2->getSocialSecurity();
            $this->fail();
        } catch (lmbARNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    function testLoadNonExistingChildObject_NOT_ThrowsException_IfSpecialFlagUsedInRelationDefinition()
    {
        $person = $this->creator->initPerson();
        $number = $this->creator->initSocialSecurity();
        $person->setSocialSecurity($number);
        $person->save();

        $this->db->delete('social_security_for_test', 'id = ' . $number->getId());

        $person2 = lmbActiveRecord:: findById(PersonForTestWithNotRequiredSocialSecurity::class, $person->getId());

        $this->assertNull($person2->getSocialSecurity());
    }

    function testGenericGetLoadsParentObject()
    {
        $person = $this->creator->initPerson();
        $number = $this->creator->initSocialSecurity();
        $person->setSocialSecurity($number);
        $person_id = $person->save();

        $person2 = lmbActiveRecord::findById(PersonForTestObject::class, $person_id);
        $number2 = $person2->get('social_security');

        $this->assertEquals($person2->getId(), $person_id);
        $this->assertEquals($person2->getName(), $person->getName());
        $this->assertEquals($number2->getId(), $number->getId());
        $this->assertEquals($number2->getCode(), $number->getCode());
    }

    function testParentRemovalDeletesChildren()
    {
        $person = $this->creator->initPerson();
        $number = $this->creator->initSocialSecurity();
        $person->setSocialSecurity($number);

        $person_id = $person->save();
        $this->assertEquals(1, $number_id = $number->getId());

        $person->destroy();

        $this->assertNull(lmbActiveRecord::findFirst(SocialSecurityForTestObject::class, array('criteria' => lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '= ' . $number_id)));
        $this->assertNull(lmbActiveRecord::findFirst(PersonForTestObject::class, array('criteria' => lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '= ' . $person_id)));
    }

    function testParentDeleteAllDeletesChildren()
    {
        $person = $this->creator->initPerson();
        $number = $this->creator->initSocialSecurity();
        $person->setSocialSecurity($number);
        $person_id = $person->save();

        $number_id = $number->getId();

        //this one should stay
        $untouched_number = $this->creator->initSocialSecurity();
        $untouched_number->save();

        lmbActiveRecord:: delete(PersonForTestObject::class);

        $this->assertNull(lmbActiveRecord:: findFirst(SocialSecurityForTestObject::class, array('criteria' => lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '= ' . $number_id)));
        $this->assertNull(lmbActiveRecord:: findFirst(PersonForTestObject::class, array('criteria' => lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '= ' . $person_id)));

        $number2 = lmbActiveRecord:: findById(SocialSecurityForTestObject::class, $untouched_number->getId());
        $this->assertEquals($number2->getCode(), $untouched_number->getCode());
    }

    function testParentRemovalWithNoCascadeDeleteChildren()
    {
        $person = new PersonForTestNoCascadeDelete();
        $person->setName('Jim');

        $number = $this->creator->initSocialSecurity();
        $person->setSocialSecurity($number);
        $person_id = $person->save();

        $this->assertEquals(1, $number_id = $number->getId());

        $person->destroy();

        $ss2 = lmbActiveRecord:: findFirst(SocialSecurityForTestObject::class, array('criteria' => lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '= ' . $number_id));
        $this->assertEquals($ss2->getCode(), $number->getCode());
    }

    function testChildRemovalNullifyParentField()
    {
        $person = $this->creator->initPerson();
        $number = $this->creator->initSocialSecurity();
        $person->setSocialSecurity($number);
        $number->setPerson($person);
        $person->save();

        $number->destroy();

        $person2 = new PersonForTestObject($person->getId());
        $this->assertNull($person2->get('ss_id'));
    }

    function testChildRemovalWithRequiredObjectInParentRelationDefinitionThrowsValidationException()
    {
        $number = $this->creator->initSocialSecurity();

        $person = new PersonForTestWithRequiredSocialSecurity();
        $person->setName('Jim');

        $person->setSocialSecurity($number);
        $number->setPerson($person);
        $person->save();

        try {
            $number->destroy();
            $this->fail();
        } catch (lmbValidationException $e) {
            $this->assertTrue(true);
        }

        $number2 = lmbActiveRecord::findFirst(SocialSecurityForTestObject::class);
        $this->assertNotNull($number2, 'Removal should not be finished');
        $this->assertEquals(
            $number2->getId(),
            $number->getId()
        );
    }

    function testSettingNullDetachesChildObject()
    {
        $person = $this->creator->initPerson();
        $number = $this->creator->initSocialSecurity();
        $person->setSocialSecurity($number);
        $person->save();

        $person->setSocialSecurity(null);
        $person_id = $person->save();

        $person2 = new PersonForTestObject($person_id);
        $this->assertNull($person2->getSocialSecurity());

        $number2 = new SocialSecurityForTestObject($number->getId());
        $this->assertEquals($number2->getCode(), $number->getCode());
    }

    function testDontResetParentIfChildImport()
    {
        $person = $this->creator->initPerson();
        $number = $this->creator->initSocialSecurity();
        $person->setSocialSecurity($number);
        $person->save();

        $source = array('name' => $person->getName());

        $person2 = new PersonForTestObject($person->getid());
        $person2->save();

        $this->assertEquals($person2->getName(), $person->getName());
        $this->assertEquals($person2->getSocialSecurity()->getCode(), $number->getCode());
    }
}
