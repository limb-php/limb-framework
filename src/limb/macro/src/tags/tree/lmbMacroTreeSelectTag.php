<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\tags\tree;

use limb\macro\src\tags\form\lmbMacroSelectTag;
use limb\i18n\src\lmbI18n;

/**
 * analog for {{select}} tag to creating <select> tags for generating tree
 * @tag tree_select
 * @package macro
 * @version $Id$
 */
class lmbMacroTreeSelectTag extends lmbMacroSelectTag
{
    public function preParse($compiler)
    {
        $this->widget_class_name = 'limb\macro\src\tag\tree\lmbMacroTreeSelectWidget';

        parent::preParse($compiler);
    }

    function _generateContent($code_writer)
    {
        $select = $this->getRuntimeVar();

        if (!$this->has('first_option'))
            $title = lmbI18n::translate('Parent select');
        else
            $title = $this->get('first_option');

        $code_writer->writePHP("{$select}->prependToOptions('0', array('title' => '$title', 'path' => null, 'level' => null));\n");

        parent::_generateContent($code_writer);
    }
}
