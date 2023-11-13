<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace Tests\active_record\cases;

use limb\validation\src\exception\lmbValidationException;
use limb\validation\src\lmbErrorList;
use limb\validation\src\lmbValidator;
use Tests\active_record\cases\src\lmbActiveRecordValidationStub;
use Tests\active_record\cases\src\TestOneTableObjectFailing;

class lmbARValidationTest extends lmbARBaseTestCase
{
  protected $tables_to_cleanup = array('test_one_table_object');
  
  function testGetErrorListReturnDefaultErrorList()
  {
    $object = $this->_createActiveRecord();
    $this->assertInstanceOf(lmbErrorList::class, $object->getErrorList());
  }

  function testValidateNew()
  {
    $error_list = new lmbErrorList();
    $insert_validator = $this->createMock(lmbValidator::class);
    $update_validator = $this->createMock(lmbValidator::class);

    $object = $this->_createActiveRecord();
    $object->setInsertValidator($insert_validator);
    $object->setUpdateValidator($update_validator);

    $object->set('annotation', 'blah-blah');

    $insert_validator
        ->expects($this->once())
        ->method('setErrorList')
        ->with($error_list);
    $insert_validator
        ->expects($this->once())
        ->method('validate')
        ->with($object);

    $insert_validator->method('validate')->willReturn(true);

    $update_validator
        ->expects($this->never())
        ->method('setErrorList');
    $update_validator
        ->expects($this->never())
        ->method('validate');

    $this->assertTrue($object->validate($error_list));
  }

  function testGetErrorListReturnLastErrorListUsed()
  {
    $error_list = new lmbErrorList();
    $insert_validator = $this->createMock(lmbValidator::class);
    $object = $this->_createActiveRecord();
    $object->setInsertValidator($insert_validator);
    $insert_validator->method('validate')->willReturn(true);
    $object->validate($error_list);

    $this->assertEquals($object->getErrorList(), $error_list);
  }

  function testValidateNewFailed()
  {
    $error_list = new lmbErrorList();
    $insert_validator = $this->createMock(lmbValidator::class);

    $object = $this->_createActiveRecord();
    $object->setInsertValidator($insert_validator);

    $insert_validator
        ->expects($this->once())
        ->method('setErrorList')
        ->with($error_list);
    $insert_validator
        ->expects($this->once())
        ->method('validate')
        ->with($object);
    $error_list->addError('foo');//simulating validation error

    $this->assertFalse($object->validate($error_list));
  }

  function testValidateExisting()
  {
    $error_list = new lmbErrorList();
    $insert_validator = $this->createMock(lmbValidator::class);
    $update_validator = $this->createMock(lmbValidator::class);

    $object = $this->_createActiveRecordWithDataAndSave();
    $object->setInsertValidator($insert_validator);
    $object->setUpdateValidator($update_validator);

    $update_validator
        ->expects($this->once())
        ->method('setErrorList')
        ->with($error_list);
    $update_validator
        ->expects($this->once())
        ->method('validate')
        ->with($object);

    $update_validator->method('validate')->willReturn(true);

    $insert_validator
        ->expects($this->never())
        ->method('setErrorList');
    $insert_validator
        ->expects($this->never())
        ->method('validate');

    $this->assertTrue($object->validate($error_list));
  }

  function testValidateExistingFailed()
  {
    $error_list = new lmbErrorList();
    $update_validator = $this->createMock(lmbValidator::class);

    $object = $this->_createActiveRecordWithDataAndSave();
    $object->setUpdateValidator($update_validator);

    $update_validator
        ->expects($this->once())
        ->method('setErrorList')
        ->with($error_list);
    $update_validator
        ->expects($this->once())
        ->method('validate')
        ->with($object);
    $error_list->addError('foo');//simulating validation error

    $this->assertFalse($object->validate($error_list));
  }

  function testDontInsertOnValidationError()
  {
    $object = $this->_createActiveRecord();

    $error_list = new lmbErrorList();

    $validator = $this->createMock(lmbValidator::class);

    $object->setInsertValidator($validator);

    $object->set('annotation', $annotation = 'Super annotation');
    $object->set('content', $content = 'Super content');
    $object->set('news_date', $news_date = '2005-01-10');

    $validator
        ->expects($this->once())
        ->method('setErrorList')
        ->with($error_list);
    $validator
        ->expects($this->once())
        ->method('validate')
        ->with($object);

    $error_list->addError('foo');//simulating validation error

    try
    {
      $object->save($error_list);
      $this->fail();
    }
    catch(lmbValidationException $e)
    {
      $this->assertEquals($e->getErrorList(), $error_list);
    }

    $this->assertEquals(0, $this->db->count('test_one_table_object'));
  }

  function testInsertOnValidationSuccess()
  {
    $object = $this->_createActiveRecord();

    $error_list = new lmbErrorList();

    $validator = $this->createMock(lmbValidator::class);
    $object->setInsertValidator($validator);

    $object->set('annotation', $annotation = 'Super annotation');
    $object->set('content', $content = 'Super content');
    $object->set('news_date', $news_date = '2005-01-10');

    $validator
        ->expects($this->once())
        ->method('setErrorList')
        ->with($error_list);
    $validator
        ->expects($this->once())
        ->method('validate')
        ->with($object);

    $object->save($error_list);

    $this->assertEquals(1, $this->db->count('test_one_table_object'));
  }

  function testDoubleInsert_FirstSaveValidationError_But_SecondSaveIsOk()
  {
    $object = $this->_createActiveRecord();

    $validator = $this->createMock(lmbValidator::class);
    $object->setInsertValidator($validator);

    $object->set('annotation', $annotation = 'Super annotation');
    $object->set('content', $content = 'Super content');
    $object->set('news_date', $news_date = '2005-01-10');

    $error_list = $this->createMock(lmbErrorList::class);
    $error_list
        ->method('isValid')
        ->willReturnOnConsecutiveCalls(
            false,
            true
        );
    
    try
    {
      $object->save($error_list);
      $this->fail();
    }
    catch(lmbValidationException $e)
    {
      $this->assertTrue(true);
    }

    $this->assertEquals(0, $this->db->count('test_one_table_object'));
    
    $object->save($error_list);

    $this->assertEquals(1, $this->db->count('test_one_table_object'));
  }
  
  function testDontUpdateOnValidationError()
  {
    $object = $this->_createActiveRecordWithDataAndSave();
    $old_annotation = $object->get('annotation');

    $error_list = new lmbErrorList();

    $validator = $this->createMock(lmbValidator::class);
    $object->setUpdateValidator($validator);

    $object->set('annotation', $annotation = 'New annotation ' . time());

    $validator
        ->expects($this->once())
        ->method('setErrorList')
        ->with($error_list);
    $validator
        ->expects($this->once())
        ->method('validate')
        ->with($object);

    $error_list->addError('foo');//simulating validation error

    try
    {
      $object->save($error_list);
      $this->fail();
    }
    catch(lmbValidationException $e)
    {
      $this->assertEquals($e->getErrorList(), $error_list);
    }

    $record = $this->db->selectRecord('test_one_table_object');
    $this->assertEquals($record->get('annotation'), $old_annotation);
  }

  function testUpdateOnValidationSuccess()
  {
    $object = $this->_createActiveRecordWithDataAndSave();

    $error_list = new lmbErrorList();

    $validator = $this->createMock(lmbValidator::class);
    $object->setUpdateValidator($validator);

    $object->set('annotation', $annotation = 'New annotation ' . time());

    $validator
        ->expects($this->once())
        ->method('setErrorList')
        ->with($error_list);
    $validator
        ->expects($this->once())
        ->method('validate')
        ->with($object);

    $validator
        ->method('validate')
        ->willReturn(true);

    $object->save($error_list);

    $record = $this->db->selectRecord('test_one_table_object');
    $this->assertEquals($record->get('annotation'), $annotation);
  }
  
  function testDoubleUpdate_FirstSaveValidationError_But_SecondSaveIsOk()
  {
    $object = $this->_createActiveRecordWithDataAndSave();

    $validator = $this->createMock(lmbValidator::class);
    $object->setUpdateValidator($validator);

    $object->set('annotation', $annotation = 'Other annotation');

    $error_list = $this->createMock(lmbErrorList::class);
    $error_list
        ->method('isValid')
        ->willReturnOnConsecutiveCalls(
            false,
            true
        );
    
    try
    {
      $object->save($error_list);
      $this->fail();
    }
    catch(lmbValidationException $e)
    {
      $this->assertTrue(true);
    }

    $record = $this->db->selectRecord('test_one_table_object');
    $this->assertNotEquals($record->get('annotation'), $annotation);
    
    $object->save($error_list);

    $record = $this->db->selectRecord('test_one_table_object');
    $this->assertEquals($record->get('annotation'), $annotation);
  }  

  function testSaveSkipValidation()
  {
    $object = $this->_createActiveRecordWithDataAndSave();

    $validator = $this->createMock(lmbValidator::class);
    $object->setUpdateValidator($validator);

    $object->set('annotation', $annotation = 'New annotation ' . time());

    $validator
        ->expects($this->never())
        ->method('validate');

    $object->saveSkipValidation();

    $record = $this->db->selectRecord('test_one_table_object');
    $this->assertEquals($record->get('annotation'), $annotation);
  }

  function testIsValid()
  {
    $object = $this->_createActiveRecordWithDataAndSave();
    $this->assertTrue($object->isValid());
  }

  function testIsNotValid()
  {
    $error_list = new lmbErrorList();

    $object = $this->_createActiveRecordWithDataAndSave();
    $this->assertTrue($object->isValid());

    $error_list->addError('whatever');//actually it's a dirty simulation but that's how it works really

    $object->save($error_list);
    $this->assertFalse($object->isValid());
  }

  function testValidationExceptionIsNotAddedToErrorList()
  {
    $error_list = new lmbErrorList();

    $object = new TestOneTableObjectFailing();
    $object->setContent('A-a-a-a');
    $object->fail = new lmbValidationException('foo', $error_list);

    $this->assertFalse($object->trySave($error_list));
    $this->assertTrue($error_list->isEmpty());
  }

  function testNonValidationExceptionIsAddedToErrorList()
  {
    $error_list = new lmbErrorList();

    $object = new TestOneTableObjectFailing();
    $object->setContent('A-a-a-a');
    $object->fail = new \Exception('yo-yo');

    $this->assertFalse($object->trySave($error_list));
    $this->assertFalse($error_list->isEmpty());
    $this->assertEquals(1, sizeof($error_list));
    $this->assertMatchesRegularExpression('~yo-yo~', $error_list[0]['message']);
  }

  function _createActiveRecord()
  {
    $object = new lmbActiveRecordValidationStub();
    return $object;
  }

  protected function _createActiveRecordWithDataAndSave()
  {
    $object = $this->_createActiveRecord();
    $object->set('annotation', 'Annotation ' . time());
    $object->set('content', 'Content ' . time());
    $object->set('news_date', date("Y-m-d", time()));
    $object->save();

    return $object;
  }
}
