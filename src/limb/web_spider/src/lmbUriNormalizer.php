<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_spider\src;

/**
 * class lmbUriNormalizer.
 *
 * @package web_spider
 * @version $Id: lmbUriNormalizer.php 7686 2009-03-04 19:57:12Z
 */

use Psr\Http\Message\UriInterface;

class lmbUriNormalizer
{
    protected $strip_anchor;
    protected $stripped_query_items;

    function __construct()
    {
        $this->reset();
    }

    function reset()
    {
        $this->strip_anchor = true;
        $this->stripped_query_items = array();
    }

    function stripAnchor($status = true)
    {
        $this->strip_anchor = $status;
    }

    function stripQueryItem($key)
    {
        $this->stripped_query_items[] = $key;
    }

    function process(UriInterface $uri): UriInterface
    {
        if ($this->strip_anchor) {
            $uri = $uri->withFragment('');
        }

        foreach ($this->stripped_query_items as $key) {
            $uri = $uri->withoutQueryItem($key);
        }

        return $uri;
    }
}
