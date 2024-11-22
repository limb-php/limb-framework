<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_spider\src;

/**
 * class lmbUriNormalizerDecorator.
 *
 * @package web_spider
 * @version $Id: lmbUriNormalizerDecorator.php 7686 2009-03-04 19:57:12Z
 */
class lmbUriNormalizerDecorator
{
    var $decorated;

    function lmbUriNormalizerDecorator(&$decorated)
    {
        $this->decorated =& $decorated;
    }

    function reset()
    {
        $this->decorated->reset();
    }

    function stripAnchor($status = true)
    {
        $this->decorated->stripAnchor($status);
    }

    function stripQueryItem($key)
    {
        $this->decorated->stripQueryItem($key);
    }

    function process($uri)
    {
        $this->decorated->process($uri);
    }
}
