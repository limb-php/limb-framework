<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_agent\src;

/**
 * Web agent cookies
 *
 * @package web_agent
 * @version $Id: lmbWebAgentCookieIterator.php 39 2007-10-03 21:08:36Z CatMan $
 */
class lmbWebAgentCookieIterator implements \Iterator
{

    protected $cookies;

    function __construct($cookies)
    {
        $this->cookies = $cookies;
    }

    function rewind(): void
    {
        reset($this->cookies);
    }

    #[ReturnTypeWillChange]
    function current()
    {
        return current($this->cookies);
    }

    #[ReturnTypeWillChange]
    function key()
    {
        return key($this->cookies);
    }

    function next(): void
    {
        next($this->cookies);
    }

    function valid(): bool
    {
        return $this->current() !== false;
    }

}
