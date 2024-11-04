<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_agent;

/**
 * Web agent cookies
 *
 * @package web_agent
 * @version $Id: lmbWebAgentCookies.php 40 2007-10-04 15:52:39Z CatMan $
 */
class lmbWebAgentCookies implements \IteratorAggregate
{

    protected $cookies;

    function __construct($cookies = array())
    {
        $this->cookies = $cookies;
    }

    function set($name, $value): void
    {
        $this->cookies[$name] = $value;
    }

    function get($name)
    {
        return $this->has($name) ? $this->cookies[$name] : '';
    }

    function has($name): bool
    {
        return isset($this->cookies[$name]);
    }

    function hasCookies(): bool
    {
        return (boolean)$this->cookies;
    }

    function export()
    {
        $cookies = array();
        foreach ($this->cookies as $name => $value)
            $cookies[] = $name . '=' . rawurlencode($value);
        return implode('; ', $cookies);
    }

    function getIterator(): \Iterator
    {
        return new lmbWebAgentCookieIterator($this->cookies);
    }

    function clean(): void
    {
        $this->cookies = array();
    }
}
