<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\tags\tree;

use limb\macro\src\tags\form\lmbMacroSingleSelectWidget;

/**
 * Represents an HTML select tag where only a single option can for tree objects
 * be selected at runtile
 * @package macro
 * @version $Id$
 */
class lmbMacroTreeSelectWidget extends lmbMacroSingleSelectWidget
{

    protected $skip_render = array('value', 'options', 'value_field', 'restricted_branch');

    function renderOptions()
    {
        $value = $this->getValue();

        $restricted_branch = $this->getAttribute('restricted_branch');
        if (!is_array($restricted_branch) && $restricted_branch != NULL)
            $restricted_branch = array($restricted_branch);

        foreach ($this->options as $key => $option) {
            //special case, since in PHP "0 == 'bar'"
            $selected = ((string)$key) == $value;

            $this->_renderOption($key, $option, $selected, $restricted_branch);
        }
    }

    protected function _renderOption($key, $option, $selected, $restricted_branch)
    {
        echo '<option value="';
        echo htmlspecialchars($key, ENT_QUOTES);
        echo '"';
        if ($selected && !in_array($key, array($restricted_branch)))
            echo " selected=\"selected\"";

        if ($restricted_branch) {
            $condition = '#';
            $length = sizeof($restricted_branch) - 1;

            foreach ($restricted_branch as $k => $item_id) {
                $condition .= '\/' . $item_id . '\/|^' . $item_id . '\/' . ($k < $length ? "|" : "#");
            }

            echo(preg_match($condition, $option['path']) ? ' disabled="true"' : '');
        }
        echo '>';

        for ($i = 1; $i <= $option['level']; $i++) {
            echo ' - ';
        }
        echo htmlspecialchars($option['title'], ENT_QUOTES) . ($key != 'choose_parent' ? ' (' . $option['uri'] . ') ' : '');

        echo '</option>';
    }
}


