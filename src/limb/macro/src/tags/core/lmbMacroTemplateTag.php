<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\tags\core;

use limb\macro\src\compiler\lmbMacroPassiveTag;

/**
 * @tag template
 * @req_attributes name
 * @package macro
 * @version $Id$
 */
class lmbMacroTemplateTag extends lmbMacroPassiveTag
{
    protected $method;
    protected $current_apply_tag = null;

    function preParse($compiler): void
    {
        if ($this->has('name'))
            $this->set('id', 'template_' . $this->get('name'));

        parent::preParse($compiler);
    }

    function generateNow($code_writer, $wrap_with_method = true)
    {
        if ($wrap_with_method) {
            $args = $code_writer->generateVar();
            $this->method = '_template' . self::generateUniqueId();
            $code_writer->beginMethod($this->getMethod(), array($args . '= array()'));
            $code_writer->writePHP("if($args) extract($args);");
            parent::generateNow($code_writer);
            $code_writer->endMethod();
        } else
            parent::generateNow($code_writer);
    }

    function generateFromDynamicAppply($code)
    {
        $this->generateNow($code, $wrap_with_method = true);

        // @TODO: remove this. in lmbMacroCodeWriter::renderCode() we added $__template_tags
        //$code->writeToInit('if(!isset($this->__template_tags)) $this->__template_tags = array();');
        //$code->writeToInit("\n");

        $code->writeToInit('$this->__template_tags["' . $this->get('name') . '"] = "' . $this->getMethod() . '";');
        $code->writeToInit("\n");
    }

    function setCurrentApplyTag(lmbMacroApplyTag $apply_tag)
    {
        $this->current_apply_tag = $apply_tag;
    }

    function getCurrentApplyTag()
    {
        return $this->current_apply_tag;
    }

    function getMethod()
    {
        return $this->method;
    }
}
