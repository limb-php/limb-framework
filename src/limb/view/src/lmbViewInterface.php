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
 * interface lmbViewInterface.
 *
 * @package view
 * @version $Id$
 */
interface lmbViewInterface
{

    //function __construct($template_name, $vars = array());

    static function locateTemplateByAlias($alias);

    function setVariables($vars): self;

    function getVariables(): array;
    
    function render();

}
