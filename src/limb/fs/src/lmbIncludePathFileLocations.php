<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\fs\src;

/**
 * class lmbIncludePathFileLocations.
 *
 * @package fs
 * @version $Id$
 */
class lmbIncludePathFileLocations implements lmbFileLocationsInterface
{
    protected $paths;

    function __construct($paths = array())
    {
        $this->paths = $paths;
    }

    function getLocations($params = array())
    {
        $resolved = array();
        foreach ($this->paths as $path) {
            foreach (lmbFs::glob($path) as $dir)
                $resolved[] = $dir;
        }
        return $resolved;
    }
}
