<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_spider\src;

/**
 * class lmbContentTypeFilter.
 *
 * @package web_spider
 * @version $Id: lmbContentTypeFilter.php 7686 2009-03-04 19:57:12Z
 */
class lmbContentTypeFilter
{
    protected $allowed_types;

    function __construct()
    {
        $this->reset();
    }

    function reset()
    {
        $this->allowed_types = array();
    }

    function allowContentType($type)
    {
        $this->allowed_types[] = strtolower($type);
    }

    function canPass($type): bool
    {
        if (!in_array($type, $this->allowed_types))
            return false;

        return true;
    }
}
