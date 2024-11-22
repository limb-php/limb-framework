<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\view\src;

use limb\core\src\exception\lmbException;
use limb\core\src\lmbEnv;
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
    const EXTENSION = '.php';

    function __construct($template_name, $vars = array())
    {
        $pos = strrpos($template_name, self::EXTENSION);
        if ($pos === false) {
            $template_name .= self::EXTENSION;
        }

        parent::__construct($template_name, $vars);
    }

    static function locateTemplateByAlias($alias)
    {
        if (!lmbFs::isPathAbsolute($alias))
            return lmbToolkit::instance()->tryFindFileByAlias($alias, ['template', lmbEnv::get('LIMB_VAR_DIR')], 'php');
        elseif (file_exists($alias))
            return $alias;

        return false;
    }

    function render()
    {
        extract($this->getVariables());
        ob_start();

        $path = self::locateTemplateByAlias($alias = $this->getTemplate());
        if(!$path)
            throw new lmbException('PHPView: unable to find template "' . $alias . '"');

        include($path);

        $res = ob_get_contents();
        ob_end_clean();
        return $res;
    }
}
