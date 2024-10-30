<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\view;

use limb\core\exception\lmbException;
use limb\core\lmbEnv;
use limb\fs\lmbFs;
use limb\toolkit\lmbToolkit;

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
