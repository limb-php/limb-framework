<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright © 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\wysiwyg\macro;

use limb\macro\tags\form\lmbMacroTextAreaTag;
use limb\wysiwyg\lmbWysiwygConfigurationHelper;

/**
 * Macro wysiwyg tag
 * @tag wysiwyg
 * @package wysiwyg
 * @version $Id$
 */
class lmbMacroWysiwygTag extends lmbMacroTextAreaTag
{
    /**
     * @var lmbWysiwygConfigurationHelper
     */
    protected $_helper;

    function preParse($compiler): void
    {
        parent::preParse($compiler);

        // always has closing tag
        $this->has_closing_tag = true;

        $this->_helper = new lmbWysiwygConfigurationHelper();
        if ($profile_name = $this->get('profile'))
            $this->_helper->setProfileName($profile_name);

        $this->_determineWidget();
    }

    protected function _determineWidget()
    {
        $component_info = $this->_helper->getMacroWidgetInfo();
        $this->widget_class_name = $component_info['class'];

        $this->html_tag = 'wysiwyg';
        $this->has_closing_tag = false;
        $this->set('profile_name', $this->_helper->getProfileName());
    }

    // rewriting parent behaviour since we don't need to render <wysiwyg> tag
    protected function _generateOpeningTag($code_writer)
    {
        $this->_generateWidget($code_writer);
    }

    protected function _generateClosingTag($code_writer)
    {
    }

    protected function _generateContent($code_writer)
    {
        $widget = $this->getRuntimeVar();
        $code_writer->writePHP("{$widget}->renderWysiwyg();\n");
    }
}
