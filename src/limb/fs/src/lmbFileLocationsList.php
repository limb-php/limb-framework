<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\fs\src;

/**
 * class lmbFileLocationsList.
 *
 * @package fs
 * @version $Id$
 */
class lmbFileLocationsList implements lmbFileLocationsInterface
{
    protected $locations = array();

    function __construct()
    {
        if (($args = func_get_args()) > 0)
            $this->locations = $args;
    }

    function getLocations($params = array())
    {
        return $this->_collectLocations($this->locations, $params);
    }

    function _collectLocations($locations, $params)
    {
        $result = array();
        foreach ($locations as $location) {
            if (is_object($location) && $location instanceof lmbFileLocationsInterface) {
                foreach ($location->getLocations($params) as $sub_location)
                    $result[] = $sub_location;
            } elseif (!is_array($location))
                $result[] = $location;
            else
                $result = array_merge($result, $this->_collectLocations($location, $params));
        }
        return $result;
    }
}
