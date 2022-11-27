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
use limb\active_record\src\lmbAROneToManyCollection;
use limb\cache\src\lmbCacheFileWithMetaBackend;
use limb\cache\src\lmbCacheGroupDecorator;
use limb\core\src\lmbCollection;

class cachedActiveRecord extends lmbActiveRecord
{
  protected $_cache;

  function getCache()
  {
    if($this->_cache)
      return $this->_cache;

    $cache_dir = LIMB_VAR_DIR . '/cache';
    return $this->_cache = new lmbCacheGroupDecorator(new lmbCacheFileWithMetaBackend($cache_dir));
  }

  function setCache($cache)
  {
    $this->_cache = $cache;
  }

  protected function _onAfterSave()
  {
    $this->flushItemCache();
    $this->flushListCache();
    $this->_hasManyFlushCache();
  }

  protected function _onAfterDestroy()
  {
    $this->flushItemCache();
    $this->flushListCache();
    $this->_hasManyFlushCache();
  }

  protected function _find($params = array())
  {
    if(!$this->cache)
      return parent :: _find($params);

    if(isset($params['no_cache']) && $params['no_cache'])
      return parent :: _find($params);

    $hash_params = '';
    foreach($params as $pkey => $param)
    {
      if(is_numeric($param) || is_string($param))
        $hash_params .= $pkey . $param;
      else
        $hash_params .= $pkey . serialize($param);
    }

    $return_first = false;
    foreach(array_values($params) as $value)
    {
      if(is_string($value) && $value == 'first')
      {
        $return_first = true;
        break;
      }
    }

    $ttl = null;
    if(isset($params['ttl']) && $params['ttl'])
      $ttl = $params['ttl'];

    //getting from cache
    $hash = md5($hash_params);
    $group = self :: getCacheGroup($this, null, $is_single = $return_first, $ttl);

    if( false === ($res = $this->cache->get($hash, array('group' => $group))) )
    {
      $res = parent :: _find($params);

      if(!$return_first)
      {
        $rs_arr = array();
        foreach($res as $record)
          $rs_arr[] = $record;
        $res = new lmbCollection($rs_arr);
      }

      $this->cache->set($hash, $res, array('group' => $group, 'ttl' => $ttl));
    }

    return $res;
  }

  protected function _findById($id_or_arr, $throw_exception)
  {
    if(!$this->cache)
      return parent :: _findById($id_or_arr, $throw_exception);

    if(is_array($id_or_arr))
    {
      if(!isset($id_or_arr['id']))
        throw new lmbARException("Criteria attribute 'id' is required for findById");

      $params = $id_or_arr;
      //avoiding possible recursion
      unset($params['id']);
      array_unshift($params, 'first');
      $id = (int)$id_or_arr['id'];
      $params['criteria'] = $this->_db_conn->quoteIdentifier($this->_primary_key_name) . '=' . $id;
    }
    else
    {
      $id = (int)$id_or_arr;
      $params = array('first', 'criteria' => $this->_db_conn->quoteIdentifier($this->_primary_key_name) . '=' . $id);
    }

    $ttl = null;
    if(isset($params['ttl']) && $params['ttl'])
      $ttl = $params['ttl'];

    //getting from cache
    $hash = md5(serialize($params));
    $group = self :: getCacheGroup($this, $id, $is_single = true, $ttl);

    if( false === ($res = $this->cache->get($hash, array('group' => $group))) )
    {
      $params = array_merge($params, array('no_cache' => true));

      if($object = $this->_find($params))
        $res = $object;
      elseif($throw_exception)
        throw new lmbARNotFoundException(get_class($this), $id);
      else
        $res = null;

      $this->cache->set($hash, $res, array('group' => $group, 'ttl' => $ttl));
    }

    return $res;
  }

  /* */
  static function getCacheGroup($item, $id = null, $is_single = false, $with_ttl = false)
  {
    if(is_string($item))
      $class_name = $item;
    else
      $class_name = get_class($item);

    if($id)
    {
      $group = 'AR_' . $class_name . '_Item_' . $id;
    }
    else
    {
      if($is_single === true)
        $group = 'AR_' . $class_name . '_Item';
      else
        $group = 'AR_' . $class_name . '_List';
    }

    return $group . (($with_ttl)?'_ttl':'');
  }

  function flushItemCache()
  {
    if(!$this->cache)
      return;

    $this->cache->flushGroup( self :: getCacheGroup($this, $this->getId(), $is_single = true) );
    $this->cache->flushGroup( self :: getCacheGroup($this, null, $is_single = true) );
  }

  function flushListCache()
  {
    if(!$this->cache)
      return;

    $this->cache->flushGroup( self :: getCacheGroup($this, null, $is_single = false) );
  }

  protected function _hasManyFlushCache()
  {
    foreach($this->_has_many as $property => $info)
    {
      if( method_exists($info['class'], 'flushListCache') )
      {
        $object = new $info['class'](); // :(
        $object->flushItemCache();
        $object->flushListCache();
      }
    }
  }

  /* */
  function __sleep()
  {
    $vars = array_keys(get_object_vars($this));
    $vars = array_diff($vars, array('_db_conn', '_db_table', '_db_meta_info', '_db_table_fields', '_cache',
                                    '_default_sort_params', '_dirty_props', '_is_dirty', '_is_being_destroyed', '_is_being_saved',
                                    '_lazy_attributes', '_is_inheritable', '_listeners'));
    return $vars;
  }
}

class CourseForTest2 extends cachedActiveRecord
{
  protected $_db_table_name = 'course_for_test';
  protected $_has_many = array('lectures' => array('field' => 'course_id',
                                                   'class' => 'LectureForTest2'));
}

class LectureForTest2 extends cachedActiveRecord
{
  protected $_db_table_name = 'lecture_for_test';
  protected $_many_belongs_to = array('course' => array('field' => 'course_id',
                                                        'class' => 'CourseForTest2'),
                                      );
}

class lmbARCacheTest extends lmbARBaseTestCase
{
  protected $tables_to_cleanup = array('course_for_test', 'lecture_for_test');
  protected $cache;

  protected function setUp(): void
  {
    parent::setUp();

    $cache_dir = LIMB_VAR_DIR . '/cache';
    $this->cache = new lmbCacheGroupDecorator(new lmbCacheFileWithMetaBackend($cache_dir));
    //$this->cache->flush();
  }

  protected function tearDown(): void
  {
    parent::tearDown();

    //$this->cache->flush();
  }

  function testCacheHasCourse()
  {
    $lecture = $this->_initLecture();
    $this->assertTrue(isset($lecture['course']));

    $lecture->save();

    $this->cache->set($key = $lecture->getId(), $lecture, array('group' => $group = $lecture->getClass(), 'ttl' => null));

    if( false !== ($lecture2 = $this->cache->get($key, array('group' => $group))) )
    {
      $this->assertTrue(isset($lecture2['course']));
    }
    else
    {
      $this->assertTrue(false);
    }
  }

  function testCacheSaveCollection()
  {
    $course = $this->_initCourse();

    $l1 = new LectureForTest2();
    $l1->setTitle('Physics');
    $l2 = new LectureForTest2();
    $l2->setTitle('Math');

    $course->addToLectures($l1);
    $course->addToLectures($l2);

    $course->save();

    $this->cache->set($key_l1 = $l1->getId(), $l1, array('group' => $group_l1 = $l1->getClass(), 'ttl' => null));
    $this->cache->set($key_l2 = $l2->getId(), $l2, array('group' => $group_l2 = $l2->getClass(), 'ttl' => null));
    $this->cache->set($key = $course->getId(), $course, array('group' => $group = $course->getClass(), 'ttl' => null));

    if( false !== ($course2 = $this->cache->get($key, array('group' => $group))) )
    {
      $rs = $course2->getLectures();

      $rs->rewind();
      $this->assertEquals($rs->current()->getTitle(), $l1->getTitle());
      $rs->next();
      $this->assertEquals($rs->current()->getTitle(), $l2->getTitle());
    }
    else
    {
      $this->assertTrue(false);
    }

    if( false !== ($l12 = $this->cache->get($key_l1, array('group' => $group_l1))) )
    {
      $this->assertEquals($l12->getTitle(), $l1->getTitle());

      $this->assertEquals($l12->getCourse()->getTitle(), $l1->getCourse()->getTitle());
    }
    else
    {
      $this->assertTrue(false);
    }
  }

  function testCacheSaveCollectionCrash()
  {
    $course = $this->_initCourse();

    $l1 = new LectureForTest2();
    $l1->setTitle('Physics');
    $l2 = new LectureForTest2();
    $l2->setTitle('Math');

    $course->addToLectures($l1);
    $course->addToLectures($l2);

    $course->save();

    /* */
    $l12 = LectureForTest2 :: findById( $l1->getId() );
    $l122 = LectureForTest2 :: findById( $l1->getId() );
    $this->cache->set($key_l12 = $l12->getId(), $l12, array('group' => $group_l12 = $l12->getClass(), 'ttl' => null));

    if( false !== ($l12c = $this->cache->get($key_l12, array('group' => $group_l12))) )
    {
      $this->assertEquals($l12->getTitle(), $l12c->getTitle());

      $this->assertEquals($l12->getCourse()->getTitle(), $l12c->getCourse()->getTitle());
      $this->assertEquals($l122->getCourse()->getTitle(), $l12c->getCourse()->getTitle());
    }
    else
    {
      $this->assertTrue(false);
    }

    /* */
    $l12 = LectureForTest2 :: findById( $l1->getId(), array('attach' => array('course')) );
    $l122 = LectureForTest2 :: findById( $l1->getId(), array('attach' => array('course')) );
    $this->cache->set($key_l12 = $l12->getId(), $l12, array('group' => $group_l12 = $l12->getClass(), 'ttl' => null));

    if( false !== ($l12c = $this->cache->get($key_l12, array('group' => $group_l12))) )
    {
      $this->assertEquals($l12->getTitle(), $l12c->getTitle());

      $this->assertEquals($l12->getCourse()->getTitle(), $l12c->getCourse()->getTitle());
      $this->assertEquals($l122->getCourse()->getTitle(), $l12c->getCourse()->getTitle());
    }
    else
    {
      $this->assertTrue(false);
    }
  }

  function _initCourse()
  {
    $course = new CourseForTest2();
    $course->setTitle('Course'. mt_rand());
    return $course;
  }

  function _initLecture()
  {
    $lecture = new LectureForTest2();
    $lecture->setTitle('Lecture'. mt_rand());
    return $lecture;
  }
}

