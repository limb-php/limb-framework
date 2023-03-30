<?php
namespace tests\active_record\cases;

use limb\active_record\src\lmbActiveRecord;
use limb\dbal\src\toolkit\lmbDbTools;
use limb\toolkit\src\lmbToolkit;

lmbToolkit::merge(new lmbDbTools());

class TestOneTableObject extends lmbActiveRecord
{
    protected $_db_table_name = 'test_one_table_object';
}

class TestOneTableObject2 extends lmbActiveRecord
{
    protected $_db_table_name = 'test_one_table_object';

    public function getBar()
    {
        return 'foo';
    }
}

class TestOneTableObjectFailing extends lmbActiveRecord
{
    var $fail;
    protected $_db_table_name = 'test_one_table_object';

    protected function _onAfterSave()
    {
        if(is_object($this->fail))
            throw $this->fail;
    }
}

class LazyTestOneTableObject extends lmbActiveRecord
{
    protected $_db_table_name = 'test_one_table_object';
    protected $_lazy_attributes = array('annotation', 'content');
}

class PersonForTest extends lmbActiveRecord
{
    public $save_count = 0;
    protected $_has_one = array('social_security' => array('field' => 'ss_id',
        'class' => SocialSecurityForTest::class,
        'can_be_null' => true));

    function _onSave()
    {
        $this->save_count++;
    }
}

class PersonForLazyAttributesTest extends lmbActiveRecord
{
    protected $_db_table_name = 'person_for_test';
    protected $_has_one = array('lazy_object' => array('field' => 'ss_id',
        'class' => LazyTestOneTableObject::class,
        'can_be_null' => true));

    protected $_lazy_attributes = array('name');
}

class SocialSecurityForTest extends lmbActiveRecord
{
    protected $_belongs_to = array('person' => array('field' => 'ss_id',
        'class' => PersonForTest::class
    ));
}

class ProgramForTest extends lmbActiveRecord
{
    protected $_db_table_name = 'program_for_test';

    protected $_has_many = array('courses' => array('field' => 'program_id',
        'class' => CourseForTest::class
    ),

        'cached_lectures' => array('field' => 'program_id',
            'class' => LectureForTest::class
        ));
}

class CourseForTest extends lmbActiveRecord
{
    protected $_db_table_name = 'course_for_test';
    protected $_has_many = array('lectures' => array('field' => 'course_id',
        'class' => LectureForTest::class),
        'alt_lectures' => array('field' => 'alt_course_id',
            'class' => LectureForTest::class),
        'foo_lectures' => array('field' => 'course_id',
            'class' => LectureForTest::class,
            'criteria'=>'lecture_for_test.title like "foo%"'));

    protected $_many_belongs_to = array('program' => array('field' => 'program_id',
        'class' => ProgramForTest::class,
        'can_be_null' => true));

    public $save_calls = 0;

    function save($error_list = null)
    {
        parent::save();
        $this->save_calls++;
    }
}

class LectureForTest extends lmbActiveRecord
{
    protected $_db_table_name = 'lecture_for_test';
    protected $_many_belongs_to = array('course' => array('field' => 'course_id',
        'class' => CourseForTest::class
    ),
        'alt_course' => array('field' => 'alt_course_id',
            'class' => CourseForTest::class,
            'can_be_null' => true
        ),
        'cached_program' => array('field' => 'program_id',
            'class' => ProgramForTest::class
        ));

    protected $_test_validator;

    function setValidator($validator)
    {
        $this->_test_validator = $validator;
    }

    function _createValidator()
    {
        if($this->_test_validator)
            return $this->_test_validator;

        return parent::_createValidator();
    }
}

class GroupForTest extends lmbActiveRecord
{
    protected $_db_table_name = 'group_for_test';

    protected $_has_many_to_many = array('users' => array('field' => 'group_id',
        'foreign_field' => 'user_id',
        'table' => 'user_for_test2group_for_test',
        'class' => UserForTest::class
    ));

    protected $_test_validator;

    function setValidator($validator)
    {
        $this->_test_validator = $validator;
    }

    function _createValidator()
    {
        if($this->_test_validator)
            return $this->_test_validator;

        return parent::_createValidator();
    }
}

class UserForTest extends lmbActiveRecord
{
    protected $_db_table_name = 'user_for_test';

    protected $_has_many_to_many = array('groups' => array('field' => 'user_id',
        'foreign_field' => 'group_id',
        'table' => 'user_for_test2group_for_test',
        'class' => GroupForTest::class
    ),
        'cgroups' => array('field' => 'user_id',
            'foreign_field' => 'group_id',
            'table' => 'user_for_test2group_for_test',
            'class' => GroupForTest::class,
            'criteria' =>'group_for_test.title="condition"'
        ));

    protected $_has_one = array('linked_object' => array('field' => 'linked_object_id',
        'class' => TestOneTableObject::class,
        'can_be_null' => true));
}

class TestOneTableObjectWithCustomProperty extends TestOneTableObject {
    protected $custom_property = true;
}

class TestOneTableObjectWithCustomDestroy extends lmbActiveRecord
{
    protected $_db_table_name = 'test_one_table_object';

    function destroy()
    {
        parent::destroy();
        echo "destroyed!";
    }
}

class TestOneTableObjectWithHooks extends TestOneTableObject
{
    protected function _onValidate()
    {
        echo '|on_validate|';
    }

    protected function _onBeforeUpdate()
    {
        echo '|on_before_update|';
    }

    protected function _onBeforeCreate()
    {
        echo '|on_before_create|';
    }

    protected function _onBeforeSave()
    {
        echo '|on_before_save|';
    }

    protected function _onAfterSave()
    {
        echo '|on_after_save|';
    }

    protected function _onSave()
    {
        echo '|on_save|';
    }

    protected function _onUpdate()
    {
        echo '|on_update|';
    }

    protected function _onCreate()
    {
        echo '|on_create|';
    }

    protected function _onAfterUpdate()
    {
        echo '|on_after_update|';
    }

    protected function _onAfterCreate()
    {
        echo '|on_after_create|';
    }

    protected function _onBeforeDestroy()
    {
        echo '|on_before_destroy|';
    }

    protected function _onAfterDestroy()
    {
        echo '|on_after_destroy|';
    }
}

class TestOneTableObjectWithSortParams extends TestOneTableObject
{
    protected $_default_sort_params = array('id' => 'DESC');
}