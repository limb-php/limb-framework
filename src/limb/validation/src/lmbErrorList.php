<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace limb\validation\src;

use limb\core\src\lmbCollection;

/**
 * Holds a list of validation errors
 * @see \limb\validation\src\lmbErrorMessage
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
  * and values in {@link lmbErrorMessage :: getReadable()}
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
  function addError($message, $fields = array(), $values = array()): lmbErrorMessage
  {
    $error = new lmbErrorMessage($message, $fields, $values);
    if( count($fields) >= 1 ) {
        //$this->addTo($error, current($fields));
        $this->add($error, current($fields));
    }
    else {
        $this->add($error);
    }

    return $error;
  }

  /**
  * Returns FALSE is contains at least one error, otherwise returns TRUE
  * @return bool
  */
  function isValid(): bool
  {
    return $this->isEmpty();
  }
  
  /**
  * Returns all processed error list with formatted messages
  * @see lmbErrorList::addError()
  * @return array
  */
  function getReadable(): array
  {
    $result = array();
    foreach ($this as $k => $error) {
        $result[$k] = $error->getReadable();
    }

    return $result;
  }

  function renameFields($new_field_names) 
  {   
    foreach($this as $message)
      $message->renameFields($new_field_names);      
  }

  function getByKey($key)
  {
      $result = [];
      foreach($this as $k => $object) {
          if( $key == $k )
            $result[] = $object;
      }

      return $result;
  }

  function __sleep()
  {
      return array_keys(get_object_vars($this));
  }
}
