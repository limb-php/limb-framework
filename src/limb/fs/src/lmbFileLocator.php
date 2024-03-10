<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\fs\src;

use limb\fs\src\exception\lmbFileNotFoundException;

/**
 * class lmbFileLocator.
 *
 * @package fs
 * @version $Id$
 */
class lmbFileLocator
{
    protected $locations;

    function __construct(lmbFileLocationsInterface $locations)
    {
        $this->locations = $locations;
    }

    function locate($alias, $params = array())
    {
        if (lmbFs::isPathAbsolute($alias)) {
            if (file_exists($alias))
                return $alias;
            else
                $this->_handleNotResolvedAlias($alias, $params);
        }

        $paths = $this->locations->getLocations($params);
        foreach ($paths as $path) {
            if (file_exists($path . '/' . $alias))
                return $path . '/' . $alias;
        }

        $this->_handleNotResolvedAlias($alias, $paths);
    }

    function getFileLocations()
    {
        return $this->locations;
    }

    function locateAll($alias = '*')
    {
        $result = array();

        $paths = $this->locations->getLocations();
        foreach ($paths as $path) {
            if ($files = glob($path . '/' . $alias))
                $result = array_merge($result, $files);
        }

        return array_unique($result);
    }

    protected function _handleNotResolvedAlias($alias, $paths)
    {
        throw new lmbFileNotFoundException($alias, 'file alias not resolved', ['paths' => $paths]);
    }
}
