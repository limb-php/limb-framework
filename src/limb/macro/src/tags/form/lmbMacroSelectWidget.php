<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\tags\form;

/**
 * @package macro
 * @version $Id$
 */
abstract class lmbMacroSelectWidget extends lmbMacroFormElementWidget
{
    protected $options = array();

    protected $skip_render = array('value', 'options', 'value_field');

    function getName()
    {
        $name = parent::getName();
        return str_replace('[]', '', $name);
    }

    function setOptions($options)
    {
        if (!is_array($options))
            $options = array();
        $this->options = $options;
    }

    function getOptions()
    {
        return $this->options;
    }

    function addToOptions($key, $value = null)
    {
        if (is_null($value))
            $this->options[] = $key;
        else
            $this->options[$key] = $value;
    }

    function prependToOptions($key, $value = null)
    {
        if (is_null($value))
            array_unshift($this->options, $key);
        else
            $this->options = array($key => $value) + $this->options;
    }

    abstract function addToDefaultSelection($selection);

    abstract function renderOptions();

    protected function _renderOption($key, $option, $selected)
    {
        echo '<option value="';
        echo htmlspecialchars($key, ENT_QUOTES);
        echo '"';
        if (isset($option['class'])) {
            echo " class=\"{$option['class']}\"";
        }
        if (isset($option['disabled']) && $option['disabled']) {
            echo " disabled=\"disabled\"";
        }
        if ($selected) {
            echo " selected=\"selected\"";
        }
        echo '>';
        if ((!isset($option)) || ($option === false)) {
            echo htmlspecialchars($key, ENT_QUOTES);
        } elseif (is_array($option) && isset($option['text'])) {
            echo htmlspecialchars($option['text'], ENT_QUOTES);
        } else {
            echo htmlspecialchars($option, ENT_QUOTES);
        }
        echo '</option>';
    }
}
