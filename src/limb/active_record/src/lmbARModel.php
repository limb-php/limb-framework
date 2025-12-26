<?php
/*
 * Limb PHP Framework
 *
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
     * Finds one instance of object in database, this method is actually a wrapper around find()
     * @param mixed $magic_params misc magic params
     * @param object $conn database connection object
     * @return lmbActiveRecord|null
     * @see findFirst()
     */
    static function fetchFirst($magic_params = null, $conn = null)
    {
        return parent::findFirst(static::class, $magic_params, $conn);
    }

    /**
     *  Finds one instance of object in database using object id, this method is actually a wrapper around find()
     * @param integer $id object id
     * @param object $conn database connection object
     * @return lmbActiveRecord|null
     * @see findById()
     */
    static function fetchById($id, $conn = null)
    {
        return parent::findById(static::class, $id, false, $conn);
    }

    static function fetchByIdOrFail($id, $conn = null)
    {
        return parent::findById(static::class, $id, true, $conn);
    }

    /**
     *  Finds a collection of objects in database using array of object ids, this method is actually a wrapper around find()
     * @param array $ids object ids
     * @param mixed $params misc magic params
     * @param object $conn database connection object
     * @return \iterator
     * @see findByIds()
     */
    static function fetchByIds($ids, $params = null, $conn = null)
    {
        return parent::findByIds(static::class, $ids, $params, $conn);
    }


    /**
     *  Finds a collection of objects in database using raw SQL
     * @param string $sql SQL
     * @param object $conn database connection object
     * @return \iterator
     */
    static function fetchBySql($sql, $conn = null)
    {
        return parent::findBySql(static::class, $sql, $conn);
    }

    /**
     *  Finds first object in database using raw SQL
     * @param string $sql SQL
     * @param object $conn database connection object
     * @return lmbActiveRecord
     */
    static function fetchFirstBySql($sql, $conn = null)
    {
        return parent::findFirstBySql(static::class, $sql, $conn);
    }

    /**
     *  Alias for findFirstBySql
     * @param string $sql SQL
     * @param object $conn database connection object
     * @return lmbActiveRecord
     * @see findFirstBySql()
     */
    static function fetchOneBySql($sql = null, $conn = null)
    {
        return parent::findFirstBySql(static::class, $sql, $conn);
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
     *  $books = self::find('Book', array('criteria' => 'name="hey"',
     *                                                 'sort' => array('id' => 'desc'),
     *                                                 'limit' => 3));
     *  //returns a collection of all Book objects in database
     *  $books = self::find('Book');
     *  //returns one object with specified id
     *  $books = self::find('Book', 1);
     *  //returns a collection of objects which match plain text criteria
     *  $books = self::find('Book', 'name="hey"');
     *  //returns a collection of objects which match criteria with placeholders
     *  $books = self::find('Book', array('name=? and author=?', 'hey', 'bob'));
     *  //returns a collection of objects which match object criteria
     *  $books = self::find('Book',
     *                                    new lmbSQLFieldCriteria('name', 'hey'));
     *  </code>
     * @param mixed $magic_params misc magic params
     * @param object $conn database connection object
     * @return \iterator
     */
    static function fetch($magic_params = null, $conn = null)
    {
        return parent::find(static::class, $magic_params, $conn);
    }

    /**
     *  Finds all objects which satisfy the passed criteria and destroys them one by one
     * @param string|object $criteria search criteria, if not set all objects are removed
     * @param object $conn database connection object
     */
    static function deleteModel($criteria = null, $conn = null)
    {
        parent::delete(static::class, $criteria, $conn);
    }

    static function deleteRawModel($criteria = null, $conn = null)
    {
        parent::deleteRaw(static::class, $criteria, $conn);
    }

    static function updateRawModel($set = null, $criteria = null, $conn = null)
    {
        parent::updateRaw(static::class, $set, $criteria, $conn);
    }

    /* */
//    public function __call($method, $args = array())
//    {
//
//    }
}
