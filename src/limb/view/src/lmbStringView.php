<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\view\src;

/**
 * class lmbPHPView.
 *
 * @package view
 * @version $Id$
 */
class lmbStringView extends lmbView
{
    function __construct($string = '')
    {
        parent::__construct(null, $string);
    }

    function render()
    {
        return $this->getVariables();
    }

    static function locateTemplateByAlias($alias)
    {
        // TODO: Implement locateTemplateByAlias() method.
    }
}
