<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\tags\form;

use limb\macro\src\compiler\lmbMacroHtmlTagWidget;

/**
 * class lmbMacroFormWidget.
 * Handles all logic with validation errors and knows about all child form elements widgets
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroFormWidget extends lmbMacroHtmlTagWidget
{
    protected $error_list = array();

    protected $children = array();

    protected $datasource;

    function __construct($id)
    {
        parent::__construct($id);

        $this->datasource = array();
    }

    function getChild($id)
    {
        if (isset($this->children[$id]))
            return $this->children[$id];
    }

    function addChild($child)
    {
        $this->children[$child->getRuntimeId()] = $child;
    }

    function setDatasource($datasource)
    {
        $this->datasource = $datasource;
    }

    function getDatasource()
    {
        return $this->datasource;
    }

    function getLabelFor($field_id)
    {
        foreach (array_keys($this->children) as $key) {
            $child = $this->children[$key];
            if (($child instanceof lmbMacroFormLabelWidget) && ($child->getAttribute('for') == $field_id))
                return $child;
        }

        return null;
    }

    function setErrorList($error_list)
    {
        if (!($error_list instanceof lmbMacroFormErrorList) && is_array($error_list))
            $error_list = new lmbMacroFormErrorList($error_list);

        $this->error_list = $error_list;
        $this->error_list->setForm($this);

        $this->_notifyFieldsAboutErrors();
    }

    protected function _notifyFieldsAboutErrors()
    {
        foreach ($this->error_list as $key => $error) {
            $fields = [];
            if ($error instanceof \ArrayAccess) {
                foreach ($error as $item_error) {
                    $fields = array_merge($fields, $item_error['fields'] ?? []);
                }
            } else {
                $fields = $error['fields'];
            }

            foreach ($fields as $field_name) {
                if (!$field = $this->getChild($field_name))
                    continue;

                $field->setErrorState(true);
                if ($label = $this->getLabelFor($field->getRuntimeId()))
                    $label->setErrorState(true);
            }
        }
    }

    function getErrorList()
    {
        return $this->error_list;
    }

    function getErrorsListForFields($for = '')
    {
        if (!count($this->error_list))
            return array();

        $result = array();
        foreach ($this->error_list as $error) {
            if (!$for)
                $this->_appendErrorsForEachField($result, $error);
            else
                $this->_appendErrorForField($result, $error, $for);
        }

        return $result;
    }

    protected function _appendErrorsForEachField(&$result, $error)
    {
        $fields = $error['fields'];
        foreach ($fields as $alias => $field) {
            if (is_object($error))
                $field_error = clone($error);
            else
                $field_error = $error;
            $field_error['id'] = $field;
            $result[] = $field_error;
        }
    }

    protected function _appendErrorForField(&$result, $error, $field)
    {
        if (!in_array($field, $error['fields']))
            return;

        if (is_object($error))
            $field_error = clone($error);
        else
            $field_error = $error;
        $field_error['id'] = $field;
        $result[] = $field_error;
    }
}
