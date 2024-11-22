<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\net\src;

use Psr\Http\Message\UriInterface;
use limb\core\src\lmbArrayHelper;
use limb\net\src\exception\lmbMalformedURLException;

/**
 * class lmbUri.
 *
 * @package net
 * @version $Id: lmbUri.php 8124 2010-02-03 17:03:21Z
 */
class lmbUri implements UriInterface
{
    private $protocol = '';
    private $user = '';
    private $password = '';
    private $host = '';
    private $port = null;
    private $path = '';
    private $path_elements = array();
    private $query = '';
    private $query_items = array();
    private $anchor = '';

    /** @throws lmbMalformedURLException */
    public function __construct($str = '')
    {
        if ($str) {
            $this->user = '';
            $this->password = '';
            $this->host = '';
            $this->port = null;
            $this->path = '';
            $this->path_elements = array();
            $this->query = '';
            $this->query_items = array();
            $this->anchor = '';

            if ('file' === substr($str, 0, 4))
                $str = $this->_fixFileProtocol($str);

            if (!$parsed_url = parse_url($str))
                throw new lmbMalformedURLException("URI '$str' is not valid");

            foreach ($parsed_url as $key => $value) {
                switch ($key) {
                    case 'scheme':
                        $this->setProtocol($value);
                        break;

                    case 'user':
                        $this->setUser($value);
                        break;

                    case 'host':
                        $this->setHost($value);
                        break;

                    case 'port':
                        $this->setPort($value);
                        break;

                    case 'pass':
                        $this->setPassword($value);
                        break;

                    case 'path':
                        $this->setPath($value);
                        break;

                    case 'query':
                        $this->setQueryString($value);
                        break;

                    case 'fragment':
                        $this->anchor = $value;
                        break;
                }
            }
        }
    }

    protected function _fixFileProtocol($url)
    {
        $matches = array();
        if (preg_match('!^file://([a-z]?:[\\\/].*)!i', $url, $matches))
            $url = 'file:///' . $matches[1];
        return $url;
    }

    /** @deprecated
     *  use getScheme() instead
     */
    function getProtocol()
    {
        return $this->protocol;
    }

    function getUser()
    {
        return $this->user;
    }

    function getPassword()
    {
        return $this->password;
    }

    function getHost()
    {
        return $this->host;
    }

    function getPort()
    {
        return $this->port;
    }

    function getPath()
    {
        return $this->path;
    }

    function getAnchor()
    {
        return $this->anchor;
    }

    private function setProtocol($protocol)
    {
        $this->protocol = $protocol;
    }

    private function setUser($user)
    {
        $this->user = $user;
    }

    private function setPassword($password)
    {
        $this->password = $password;
    }

    private function setHost($host)
    {
        $this->host = $host ? strtolower($host) : '';
    }

    private function setPort($port)
    {
        $this->port = $port;
    }

    private function setPath($path)
    {
        $this->path = $path;
        $this->path_elements = explode('/', $this->path);
    }

    function isAbsolute(): bool
    {
        if (!strlen($this->path))
            return true;

        return ('/' == $this->path[0]);
    }

    function isRelative(): bool
    {
        return !$this->isAbsolute();
    }

    function countPath()
    {
        return sizeof($this->path_elements);
    }

    function countQueryItems()
    {
        return sizeof($this->query_items);
    }

    function __toString()
    {
        return $this->toString();
    }

    function toString($parts = array('protocol', 'user', 'password', 'host', 'port', 'path', 'query', 'anchor'))
    {
        $string = '';

        if (in_array('protocol', $parts))
            $string .= !empty($this->protocol) ? $this->protocol . '://' : '';

        if (in_array('user', $parts)) {
            $string .= $this->user;

            if (in_array('password', $parts))
                $string .= (!empty($this->password) ? ':' : '') . $this->password;

            $string .= (!empty($this->user) ? '@' : '');
        }

        if (in_array('host', $parts)) {
            $string .= $this->host;

            if (in_array('port', $parts))
                $string .= (empty($this->port) || ($this->port == '80') ? '' : ':' . $this->port);
        } else
            $string = '';

        if (in_array('path', $parts))
            $string .= $this->getPath();

        if (in_array('query', $parts)) {
            $query_string = $this->getQuery();
            $string .= !empty($query_string) ? '?' . $query_string : '';
        }

        if (in_array('anchor', $parts))
            $string .= !empty($this->anchor) ? '#' . $this->anchor : '';

        return $string;
    }

    /**
     * Sets the query_string to literally what you supply
     */
    private function setQueryString($query_string)
    {
        $this->query = $query_string;
        $this->query_items = $this->_parseQueryString($query_string);
    }

    function getQueryItem($name)
    {
        if (isset($this->query_items[$name]))
            return $this->query_items[$name];

        return false;
    }

    function getQueryItems()
    {
        return $this->query_items;
    }

    function withQueryItem($name, $value)
    {
        $query_items = $this->query_items;
        $query_items[$name] = $value;

        return $this->withQuery($this->_queryItemsToString($query_items));
    }

    function withQueryItems($items)
    {
        return $this->withQuery($this->_queryItemsToString($items));
    }

    /**
     * Removes a query_string item
     *
     */
    function withoutQueryItem($name, $index = null)
    {
        $query_items = $this->query_items;

        if (isset($query_items[$name])) {
            if (is_array($query_items[$name]) && $index)
                unset($query_items[$name][$index]);
            else
                unset($query_items[$name]);
        }

        return $this->withQuery($this->_queryItemsToString($query_items));
    }

    /**
     * Removes query items
     */
    function withoutQueryItems()
    {
        return $this->withQuery('');
    }

    /**
     * Returns flat query_string
     *
     */
    function getQueryString()
    {
        return $this->_queryItemsToString($this->query_items);
    }

    protected function _queryItemsToString($init_query_items = array())
    {
        $query_string = '';
        $query_items = array();
        $flat_array = array();

        lmbArrayHelper::toFlatArray($init_query_items, $flat_array);
        foreach ($flat_array as $key => $value) {
            if (!is_null($value))
                $query_items[] = $key . '=' . urlencode($value);
            else
                $query_items[] = $key;
        }

        if ($query_items)
            $query_string = implode('&', $query_items);

        return $query_string;
    }

    /**
     * Parses raw query_string and returns an array of it
     */
    protected function _parseQueryString($query_string)
    {
        parse_str($query_string, $arr);

        foreach ($arr as $key => $item) {
            if (!is_array($item))
                $arr[$key] = rawurldecode($item);
        }

        return $arr;
    }

    public function getScheme(): string
    {
        return $this->protocol;
    }

    public function getAuthority()
    {
        $authority = $this->getHost();
        if ($this->getUserInfo() !== '') {
            $authority = $this->getUserInfo() . '@' . $authority;
        }
        if ($this->getPort()) {
            $authority .= ':' . $this->getPort();
        }
        return $authority;
    }

    public function getUserInfo()
    {
        $userInfo = $this->getUser();
        if ($this->getPassword())
            $userInfo .= ':' . $this->getPassword();

        return $userInfo;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getFragment()
    {
        return $this->anchor;
    }

    public function withScheme($scheme)
    {
        if ($this->getScheme() === $scheme)
            return $this;

        $clone = clone($this);
        $clone->setProtocol($scheme);

        return $clone;
    }

    public function withUserInfo($user, $password = null)
    {
        $clone = clone($this);
        $clone->setUser($user);
        $clone->setPassword($password);

        return $clone;
    }

    public function withHost($host)
    {
        if ($this->getHost() === $host)
            return $this;

        $clone = clone($this);
        $clone->setHost($host);

        return $clone;
    }

    public function withPort($port)
    {
        if ($this->getPort() === $port)
            return $this;

        $clone = clone($this);
        $clone->setPort($port);

        return $clone;
    }

    public function withPath($path)
    {
        if ($this->getPath() === $path)
            return $this;

        $clone = clone($this);
        $clone->setPath($path);

        return $clone;
    }

    public function withQuery($query)
    {
        if ($this->getQuery() === $query)
            return $this;

        $clone = clone($this);
        $clone->setQueryString($query);

        return $clone;
    }

    public function withFragment($fragment)
    {
        if ($this->getAnchor() === $fragment)
            return $this;

        $clone = clone($this);
        $clone->anchor = $fragment;

        return $clone;
    }
}
