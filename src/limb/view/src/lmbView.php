<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\view\src;

/**
 * abstract class lmbView.
 *
 * @package view
 * @version $Id$
 */
abstract class lmbView implements lmbViewInterface
{
    protected $template_name;
    protected $variables = array();
    protected $forms_datasources = array();
    protected $forms_errors = array();

    function __construct($template_name, $vars = array())
    {
        $this->template_name = $template_name;
        $this->variables = $vars;
    }

    abstract function render();

    function reset()
    {
        $this->forms_datasources = array();
        $this->forms_errors = array();
        $this->variables = array();
    }

    function copy($view)
    {
        $this->variables = $view->variables;
        $this->forms_errors = $view->forms_errors;
        $this->forms_datasources = $view->forms_datasources;
    }

    function getTemplate()
    {
        return $this->template_name;
    }

    function set($variable_name, $value)
    {
        $this->variables[$variable_name] = $value;
    }

    function with($variable_name, $value): self
    {
        $this->variables[$variable_name] = $value;

        return $this;
    }

    function setVariables($vars): self
    {
        $this->variables = $vars;

        return $this;
    }

    function get($variable_name)
    {
        if (isset($this->variables[$variable_name]))
            return $this->variables[$variable_name];
    }

    function getVariables(): array
    {
        return $this->variables;
    }

    function setFormDatasource($form_name, $datasource)
    {
        $this->forms_datasources[$form_name] = $datasource;
    }

    function getFormDatasource($form_name = null)
    {
        if (!$form_name)
            return $this->forms_datasources;

        if (isset($this->forms_datasources[$form_name]))
            return $this->forms_datasources[$form_name];

        return null;
    }

    function setFormErrors($form_name, $error_list)
    {
        $this->forms_errors[$form_name] = $error_list;
    }

    function getFormErrors($form_name = null)
    {
        if (!$form_name)
            return $this->forms_errors;

        if (isset($this->forms_errors[$form_name]))
            return $this->forms_errors[$form_name];

        return null;
    }

    function getForms()
    {
        return $this->forms_datasources;
    }

//    public function __toString()
//    {
//        return $this->render();
//    }
}
