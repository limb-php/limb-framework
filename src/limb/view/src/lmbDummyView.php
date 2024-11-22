<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\view\src;

/**
 * class lmbDummyView.
 *
 * @package view
 * @version $Id$
 */
class lmbDummyView extends lmbView
{
    static function locateTemplateByAlias($alias)
    {
        return $alias;
    }

    function render()
    {
        return '';
    }
}
