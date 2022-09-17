<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\active_record\src;

/**
 * @version $Id: lmbARModel
 * @package active_record
 */
class lmbARModel extends lmbActiveRecord
{
  /**
   *  Finds one instance of object in database, this method is actually a wrapper around find()
   *  @see find()
   *  @param mixed misc magic params
   *  @param object database connection object
   *  @return lmbActiveRecord|null
   */
  static function findModelFirst($magic_params = null, $conn = null)
  {
    $class_name = self::_getCallingClass();

    return parent::findFirst($class_name, $magic_params, $conn);
  }

  /**
   *  self :: findFirst() convenience alias
   *  @see findFirst()
   *  @param mixed misc magic params
   *  @return lmbActiveRecord|null
   */
  static function findModelOne($magic_params = null, $conn = null)
  {
    $class_name = self::_getCallingClass();

    return parent::findOne($class_name, $magic_params, $conn);
  }

  /**
   *  Finds one instance of object in database using object id, this method is actually a wrapper around find()
   *  @see find()
   *  @param integer object id
   *  @param object database connection object
   *  @return lmbActiveRecord|null
   */
  static function findModelById($id, $throw_exception = true, $conn = null)
  {
    $class_name = self::_getCallingClass();

    return parent::findById($class_name, $id, $throw_exception, $conn);
  }

  /**
   *  Finds a collection of objects in database using array of object ids, this method is actually a wrapper around find()
   *  @see find()
   *  @param array object ids
   *  @param mixed misc magic params
   *  @param object database connection object
   *  @return iterator
   */
  static function findModelByIds($ids = null, $params = null, $conn = null)
  {
    $class_name = self::_getCallingClass();

    return parent::findByIds($class_name, $ids, $params, $conn);
  }


  /**
   *  Finds a collection of objects in database using raw SQL
   *  @param string SQL
   *  @param object database connection object
   *  @return iterator
   */
  static function findModelBySql($sql = null, $conn = null)
  {
    $class_name = self::_getCallingClass();

    return parent::findBySql($class_name, $sql, $conn);
  }

  /**
   *  Finds first object in database using raw SQL
   *  @param string SQL
   *  @param object database connection object
   *  @return lmbActiveRecord
   */
  static function findModelFirstBySql($sql = null, $conn = null)
  {
    $class_name = self::_getCallingClass();

    return parent::findFirstBySql($class_name, $sql, $conn);
  }

  /**
   *  Alias for findFirstBySql
   *  @see findFirstBySql()
   *  @return lmbActiveRecord
   */
  static function findModelOneBySql($sql = null, $conn = null)
  {
    $class_name = self::_getCallingClass();

    return parent::findOneBySql($class_name, $sql, $conn);
  }

  /**
   *  Generic objects finder.
   *  Using misc magic params it's possible to pass different search parameters.
   *  If passed as an array magic params can have the following properties:
   *   - <b>criteria</b> - apply specified criteria to collection can be a plain string or criteria object
   *   - <b>limit,offset</b> - apply limit,offset to collection
   *   - <b>sort</b>  - sort collection by specified fields, e.g array('id' => 'desc', 'name' => 'asc')
   *   - <b>first</b> - return the first object of collection
   *  Some examples:
   *  <code>
   *  //generic way to find a collection of objects using magic params,
   *  //in this case we want collection:
   *  // - to match 'name="hey"' criteria
   *  // - ordered by 'id' property using descendant sort
   *  // - limited to 3 items
   *  $books = self :: find('Book', array('criteria' => 'name="hey"',
   *                                                 'sort' => array('id' => 'desc'),
   *                                                 'limit' => 3));
   *  //returns a collection of all Book objects in database
   *  $books = self :: find('Book');
   *  //returns one object with specified id
   *  $books = self :: find('Book', 1);
   *  //returns a collection of objects which match plain text criteria
   *  $books = self :: find('Book', 'name="hey"');
   *  //returns a collection of objects which match criteria with placeholders
   *  $books = self :: find('Book', array('name=? and author=?', 'hey', 'bob'));
   *  //returns a collection of objects which match object criteria
   *  $books = self :: find('Book',
   *                                    new lmbSQLFieldCriteria('name', 'hey'));
   *  </code>
   *  @param mixed misc magic params
   *  @param object database connection object
   *  @return iterator
   */
  static function findModel($magic_params = null, $conn = null)
  {
    $class_name = self::_getCallingClass();

    return parent::find($class_name, $magic_params, $conn);
  }

  /**
   *  Finds all objects which satisfy the passed criteria and destroys them one by one
   *  @param string|object search criteria, if not set all objects are removed
   *  @param object database connection object
   */
  static function deleteModel($criteria = null, $conn = null)
  {
    $class_name = self::_getCallingClass();

    parent::delete($class_name, $criteria, $conn);
  }

  static function deleteRawModel($criteria = null, $conn = null)
  {
    $class_name = self::_getCallingClass();

    parent::deleteRaw($class_name, $criteria, $conn);
  }

  static function updateRawModel($set = null, $criteria = null, $conn = null)
  {
    $class_name = self::_getCallingClass();

    parent::updateRaw($class_name, $set, $criteria, $conn);
  }

  /* */
  public function __call($method, $args = array()) {

  }
}
