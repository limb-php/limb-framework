<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\wysiwyg\src\macro;

use limb\wysiwyg\src\helper\FCKeditor\FCKeditor;

/**
 * @package wysiwyg
 * @version $Id$
 */
class lmbMacroFCKEditorWidget extends lmbMacroBaseWysiwygWidget
{
    protected $dir = '';

    function renderWysiwyg()
    {
        $this->_initWysiwyg();

        $this->_renderEditor();
    }

    protected function _renderEditor()
    {
        $editor = new FCKeditor($this->getAttribute('name'));

        $this->_setEditorParameters($editor);

        $editor->Value = $this->getValue();

        $editor->Create();
    }

    protected function _setEditorParameters($editor)
    {
        if ($this->_helper->getOption('base_path'))
            $editor->BasePath = $this->_helper->getOption('base_path');
        else
            $editor->BasePath = '/shared/wysiwyg/fckeditor/';

        if ($this->_helper->getOption('ToolbarSet'))
            $editor->ToolbarSet = $this->_helper->getOption('ToolbarSet');

        if ($this->_helper->getOption('Config'))
            $editor->Config = $this->_helper->getOption('Config');

        $editor->Width = $this->getAttribute('width');
        $editor->Height = $this->getAttribute('height');
    }
}
