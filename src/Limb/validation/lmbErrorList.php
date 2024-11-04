<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\validation;

use limb\core\lmbCollection;

/**
 * Holds a list of validation errors
 * @see \limb\validation\lmbErrorMessage
 * @package validation
 * @version $Id: lmbErrorList.php 7486 2009-01-26 19:13:20Z
 */
class lmbErrorList extends lmbCollection
{
    /**
     * Adds new error.
     * Creates an object of {@link lmbErrorMessage} class.
     * Accepts error message, array of fields list which this error is belong to and array of values.
     * Error message can contain placeholders like {Placeholder} that will be replaced with field names
     * and values in {@link lmbErrorMessage::getReadable()}
     * Here is an example of adding error to error list in some validation rule:
     * <code>
     *  $error_list->addError('{Field} must contain at least {min} characters.', array('Field' => 'password'), array('min' => 5));
     * </code>
     * After all replacements we can get something like "password must contain at least 5 characters"
     * @param $message string Error message with placeholders like {Field} must contain at least {min} characters.
     * @param $fields array Array of aliases and field names like array('BaseField' => 'password', 'RepeatField' => 'repeat_password')
     * @param $values array Array of aliases and field values like array('Min' => 5, 'Max' => 15)
     * @return lmbErrorMessage
     */
    function addError($message, $fields = array(), $values = array(), $validator = null): lmbErrorMessage
    {
        $error = new lmbErrorMessage($message, $fields, $values);
        if (count($fields) >= 1) {
            $key = current($fields);
            if ($validator !== null)
                $key .= '.' . $validator;

            $this->add($error, $key);
        } else {
            $this->add($error);
        }

        return $error;
    }

//    function add($item, $key = null)
//    {
//        if( $key === null ) {
//            $this->dataset[] = $item;
//        }
//        else {
//            if( !isset($this->dataset[$key]) ) {
//                $this->dataset[$key] = [];
//            }
//            $this->dataset[$key][] = $item;
//        }
//
//        $this->iteratedDataset = null;
//    }

//    function count(): int
//    {
//        return count($this->dataset, COUNT_RECURSIVE) - count($this->dataset);
//    }

    function all()
    {
        return $this->export();
    }

    function getByKey($key)
    {
        if (str_contains($key, '*')) {
            $result = [];
            foreach ($this->dataset as $k => $v) {
                if(preg_match('/'.$key.'/', $k))
                    $result[$k] = $v;
            }

            return $result;
        }

        $result = $this->dataset[$key] ?? null;
        if (is_array($result)) {
            $result = $this->dataset[$key];
        }

        return $result;
    }

    /**
     * Returns FALSE is containing at least one error, otherwise returns TRUE
     * @return bool
     */
    function isValid(): bool
    {
        return $this->isEmpty();
    }

    /**
     * Returns all processed error list with formatted messages
     *
     * @return array
     * @see lmbErrorList::addError()
     */
    function getReadable(): array
    {
        $result = [];
        foreach ($this as $key => $error) {
            /** @var $error lmbErrorMessage */
            $result[$key] = $error->getReadable();
        }

        return $result;
    }

    function renameFields($new_field_names)
    {
        foreach ($this as $error) {
            $error->renameFields($new_field_names);
        }
    }

    function __sleep()
    {
        return array_keys(get_object_vars($this));
    }
}
