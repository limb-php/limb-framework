<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_agent\src;

/**
 * Web server cookies collection
 *
 * @package web_agent
 * @version $Id: lmbWebServerCookiesCollection.php 43 2007-10-05 15:33:11Z CatMan $
 */
class lmbWebServerCookiesCollection implements \IteratorAggregate
{

    protected $cookies = array();

    function add(lmbWebServerCookie $cookie)
    {
        $num = $this->search($cookie->name, $cookie->path, $cookie->domain);
        if ($num === false)
            $this->cookies[] = $cookie;
        else
            $this->cookies[$num] = $cookie;
    }

    function search($name, $path = '', $domain = '')
    {
        foreach ($this->cookies as $n => $cookie) {
            if ($cookie->name == $name && $cookie->path == $path && $cookie->domain == $domain)
                return $n;
        }
        return false;
    }

    function get($num)
    {
        return isset($this->cookies[$num]) ? $this->cookies[$num] : false;
    }

    function getIterator()
    {
        return new lmbWebAgentCookieIterator($this->cookies);
    }

    function copyTo(lmbWebServerCookiesCollection $dest)
    {
        foreach ($this->cookies as $cookie) {
            $new_cookie = clone $cookie;
            $dest->add($new_cookie);
        }
    }

}
