<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\validation\src;

use limb\core\src\lmbObject;

/**
 * Single validation error message.
 * @package validation
 * @version $Id$
 */
class lmbErrorMessage extends lmbObject
{
    public $message;
    public $fields = array();
    public $values = array();

    function __construct($message, $fields = array(), $values = array())
    {
        parent::__construct(array('message' => $message, 'fields' => $fields, 'values' => $values));
    }

    function getReadable()
    {
        $text = $this->getMessage();
        foreach ($this->getFields() as $key => $fieldName) {
            $replacement = '"' . $fieldName . '"';
            $text = str_replace('{' . $key . '}', $replacement, $text);
        }

        foreach ($this->getValues() as $key => $replacement)
            $text = str_replace('{' . $key . '}', $replacement, $text);

        return $text;
    }

    function renameFields($new_field_names)
    {
        if (!is_array($new_field_names)) {
            return;
        }

        $new_fields = array();

        foreach ($this->getFields() as $key => $field) {
            $new_fields[$key] = $new_field_names[$field] ?? $field;
        }

        $this->setFields($new_fields);
    }

    function __toString()
    {
        return $this->getReadable();
    }
}
