<?php
/*
 * Limb PHP Framework
 *
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
