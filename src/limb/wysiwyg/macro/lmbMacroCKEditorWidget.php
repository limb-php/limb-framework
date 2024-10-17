<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright © 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\wysiwyg\macro;

/**
 * @package wysiwyg
 * @version $Id$
 */
class lmbMacroCKEditorWidget extends lmbMacroBaseWysiwygWidget
{
    function renderWysiwyg()
    {
        $this->_initWysiwyg();

        $this->_renderEditor();
    }

    protected function _renderEditor()
    {
        $editor = new \limb\wysiwyg\src\helper\CKeditor\CKeditor();

        if ($this->_helper->getOption('basePath'))
            $editor->basePath = $this->_helper->getOption('basePath');
        else
            $editor->basePath = '/shared/wysiwyg/ckeditor/';

        $config = array();
        if ($this->_helper->getOption('Config'))
            $config = $this->_helper->getOption('Config');

        $editor->editor($this->getAttribute('name'), $this->getValue(), $config);
    }
}
