<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\view\src;

use limb\core\src\exception\lmbException;

/**
 * class lmbBlitzView.
 *
 * @package view
 * @version $Id$
 */
class lmbBlitzView extends lmbView
{
    const EXTENSION = '.bhtml';

    private $templateInstance;

    static function locateTemplateByAlias($alias)
    {
        return null;
    }

    function __call($methodName, $params)
    {
        $tpl = $this->getTemplateInstance();
        if (!method_exists($tpl, $methodName)) {
            throw new lmbException(
                'Wrong template method called',
                array(
                    'template class' => get_class($tpl),
                    'method' => $methodName,
                )
            );
        }
        return call_user_func($methodName, $tpl, $params);
    }

    function getTemplateInstance()
    {
        if (!$this->templateInstance) {
            if (!class_exists('Blitz'))
                throw new lmbException("Blitz extension is not loaded");

            $this->templateInstance = new \Blitz($this->getTemplate());
        }
        return $this->templateInstance;
    }

    function render()
    {
        foreach ($this->getVariables() as $name => $value)
            $this->getTemplateInstance()->set(array($name => $value));

        return $this->getTemplateInstance()->parse();
    }

}
