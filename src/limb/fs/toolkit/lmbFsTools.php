<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\fs\toolkit;

use limb\core\lmbEnv;
use limb\toolkit\lmbAbstractTools;
use limb\fs\lmbFileLocator;
use limb\fs\lmbCachingFileLocator;
use limb\fs\lmbIncludePathFileLocations;
use limb\fs\exception\lmbFileNotFoundException;

/**
 * class lmbFsTools.
 *
 * @package fs
 * @version $Id$
 */
class lmbFsTools extends lmbAbstractTools
{
    protected $file_locators = array();

    function findFileByAlias($alias, $paths, $locator_name = null, $find_all = false)
    {
        $locator = $this->toolkit->getFileLocator($paths, $locator_name);

        if ($find_all)
            return $locator->locateAll($alias);
        else
            return $locator->locate($alias);
    }

    function tryFindFileByAlias($alias, $paths, $locator_name = null)
    {
        try {
            $file = $this->findFileByAlias($alias, $paths, $locator_name);
        } catch (lmbFileNotFoundException $e) {
            return null;
        }
        return $file;
    }

    function getFileLocator($paths, $locator_name = null): lmbFileLocator
    {
        if (!$locator_name)
            $locator_name = md5($paths);

        if (isset($this->file_locators[$locator_name]))
            return $this->file_locators[$locator_name];

        if (is_array($paths))
            $file_locations = new lmbIncludePathFileLocations($paths);
        else
            $file_locations = new lmbIncludePathFileLocations(explode(';', $paths));

        if (lmbEnv::has('LIMB_VAR_DIR') && ('devel' != lmbEnv::get('LIMB_APP_MODE')))
            $locator = new lmbCachingFileLocator(new lmbFileLocator($file_locations),
                lmbEnv::get('LIMB_VAR_DIR') . '/locators/',
                $locator_name);
        else
            $locator = new lmbFileLocator($file_locations);

        $this->file_locators[$locator_name] = $locator;
        return $locator;
    }
}
