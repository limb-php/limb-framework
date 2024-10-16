<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright © 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\wysiwyg\macro;

use limb\wysiwyg\helper\FCKeditor\FCKeditor;

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
