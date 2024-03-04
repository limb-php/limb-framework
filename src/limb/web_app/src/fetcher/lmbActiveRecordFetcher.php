<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_app\src\fetcher;

use limb\core\src\lmbCollection;
use limb\active_record\src\lmbActiveRecord;
use limb\core\src\exception\lmbException;
use limb\active_record\src\lmbARNotFoundException;
use limb\core\src\lmbString;

/**
 * class lmbActiveRecordFetcher.
 *
 * @package web_app
 * @version $Id: lmbActiveRecordFetcher.php 7486 2009-01-26 19:13:20Z
 */
class lmbActiveRecordFetcher extends lmbFetcher
{
    protected $class_name;
    protected $record_id;
    protected $record_ids;
    protected $find;
    protected $find_params = array();

    function setClassName($value)
    {
        $this->class_name = $value;
    }

    function setRecordId($value)
    {
        if (!$value)
            $value = '';
        $this->record_id = $value;
    }

    function setFind($find)
    {
        $this->find = $find;
    }

    function addFindParam($value)
    {
        $this->find_params[] = $value;
    }

    function setFindParams($find_params)
    {
        $this->find_params = $find_params;
    }

    function setRecordIds($value)
    {
        if (!is_array($value))
            $this->record_ids = array();
        else
            $this->record_ids = $value;
    }

    function _createDataSet()
    {
        if (!$this->class_name)
            throw new lmbException('Class is not defined!');

        if (is_null($this->record_id) && is_null($this->record_ids)) {
            if (!$this->find) {
                return lmbActiveRecord::find($this->class_name);
            } else {
                $method = 'find' . lmbString::camel_case($this->find);
                $callback = array($this->class_name, $method);
                if (!is_callable($callback))
                    throw new lmbException('Active record of class "' . $this->class_name . '" does not support method "' . $method . '"');
                return call_user_func_array($callback, $this->find_params);
            }
        }

        if ($this->record_id) {
            try {
                if ($this->find) {
                    $method = 'find' . lmbString::camel_case($this->find);
                    $callback = array($this->class_name, $method);
                    if (!is_callable($callback))
                        throw new lmbException('Active record of class "' . $this->class_name . '" does not support method "' . $method . '"');
                    $record = call_user_func_array($callback, array($this->record_id));
                } else
                    $record = lmbActiveRecord::findById($this->class_name, $this->record_id);
            } catch (lmbARNotFoundException $e) {
                $record = array();
            }

            return $this->_singleItemCollection($record);
        } elseif ($this->record_ids) {
            return lmbActiveRecord::findByIds($this->class_name, $this->record_ids);
        }

        return new lmbCollection();
    }

    protected function _singleItemCollection($ar)
    {
        return new lmbCollection(array($ar));
    }
}
