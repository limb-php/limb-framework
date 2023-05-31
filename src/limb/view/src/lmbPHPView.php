<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

namespace limb\view\src;

use limb\fs\src\lmbFs;
use limb\toolkit\src\lmbToolkit;

/**
 * class lmbPHPView.
 *
 * @package view
 * @version $Id$
 */
class lmbPHPView extends lmbView
{
    static function locateTemplateByAlias($alias)
    {
        if(!lmbFs::isPathAbsolute($alias))
            return lmbToolkit::instance()->tryFindFileByAlias($alias, ['template'], 'php');
        elseif(file_exists($alias))
            return $alias;

        return false;
    }

    function render()
    {
        extract($this->getVariables());
        ob_start();

        include( self::locateTemplateByAlias($this->getTemplate()) );

        $res = ob_get_contents();
        ob_end_clean();
        return $res;
    }
}
