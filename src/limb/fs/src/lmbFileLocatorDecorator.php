<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\fs\src;

/**
 * class lmbFileLocatorDecorator.
 *
 * @package fs
 * @version $Id$
 */
class lmbFileLocatorDecorator extends lmbFileLocator
{
    protected $locator = null;

    function __construct($locator)
    {
        $this->locator = $locator;
    }

    function locate($alias, $params = array())
    {
        return $this->locator->locate($alias, $params);
    }

    function locateAll($alias = '')
    {
        return $this->locator->locateAll($alias);
    }

    function getFileLocations()
    {
        return $this->locator->getFileLocations();
    }
}
