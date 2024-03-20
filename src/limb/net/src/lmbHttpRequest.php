<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\net\src;

use limb\core\src\exception\lmbException;
use limb\core\src\lmbArrayHelper;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * class lmbHttpRequest.
 *
 * @package net
 * @version $Id: lmbHttpRequest.php 8122 2010-02-02 09:54:14Z
 */
class lmbHttpRequest implements \ArrayAccess, RequestInterface
{
    /**
     * @var string
     */
    protected $__version;
    protected $__method;
    /** @var lmbUri */
    protected $__uri;
    protected $__headers = array();
    protected $__get = array();
    protected $__post = array();
    protected $__cookies = array();
    protected $__files = array();
    protected $__attributes = array();
    protected $__pretend_post = false;
    /** @var null|string */
    protected $__requestTarget;
    protected $__reserved_attrs = array('__version', '__requestTarget', '__method', '__uri', '__headers', '__get', '__post', '__cookies', '__files', '__attributes', '__pretend_post', '__reserved_attrs');

    private $stream;

    function __construct($uri_string = null, $method = 'GET', $get = [], $post = [], $cookies = [], $files = [], $headers = [])
    {
        $this->_initRequestProperties($uri_string, $method, $get, $post, $cookies, $files, $headers);
    }

    /**
     * @throws lmbException
     */
    protected function _initRequestProperties($uri_string, $method, $get, $post, $cookies, $files, $headers)
    {
        $this->__version = '1.0';

        $this->__method = $method;

        $this->__uri = new lmbUri($uri_string);

        $this->__get = $get;
        $items = $this->__uri->getQueryItems();
        foreach ($items as $k => $v)
            $this->__get[$k] = $v;

        $this->__post = $post;
        $this->__cookies = $cookies;
        $this->__files = $this->_parseUploadedFiles($files);

        if (ini_get('magic_quotes_gpc')) {
            $this->__get = $this->_stripHttpSlashes($this->__get);
            $this->__post = $this->_stripHttpSlashes($this->__post);
            $this->__cookies = $this->_stripHttpSlashes($this->__cookies);
        }

        $request = lmbArrayHelper::arrayMerge($this->__get, $this->__post, $this->__files);
        foreach ($request as $k => $v) {
            if (in_array($k, $this->__reserved_attrs))
                continue;
            $this->set($k, $v);
        }

        $this->__headers = $headers;
        $this->updateHostFromUri();
    }

    static public function createFromGlobals(): self
    {
        $uri_string = self::_getRawUriString();
        $method = $_SERVER['REQUEST_METHOD'] ?? null;
        $headers = self::_readHeaders();

        return new static($uri_string, $method, $_GET, $_POST, $_COOKIE, $_FILES, $headers);
    }

    static protected function _readHeaders(): array
    {
        return getallheaders();
    }

    static protected function _getRawUriString()
    {
        $host = 'localhost';

        if (!empty($_SERVER['HTTP_HOST'])) {
            $items = explode(':', $_SERVER['HTTP_HOST']);
            $host = $items[0];
            $port = $items[1] ?? null;
        } elseif (!empty($_SERVER['SERVER_NAME'])) {
            $items = explode(':', $_SERVER['SERVER_NAME']);
            $host = $items[0];
            $port = $items[1] ?? null;
        }

        if (isset($_SERVER['HTTPS']) && !strcasecmp($_SERVER['HTTPS'], 'on'))
            $protocol = 'https';
        else
            $protocol = 'http';

        if (!isset($port) || $port != intval($port))
            $port = $_SERVER['SERVER_PORT'] ?? 80;

        if ($protocol == 'http' && $port == 80)
            $port = null;

        if ($protocol == 'https' && $port == 443)
            $port = null;

        $server = $protocol . '://' . $host . (isset($port) ? ':' . $port : '');

        if (isset($_SERVER['REQUEST_URI']))
            $url = $_SERVER['REQUEST_URI'];
        elseif (isset($_SERVER['QUERY_STRING']))
            $url = basename($_SERVER['PHP_SELF']) . '?' . $_SERVER['QUERY_STRING'];
        else
            $url = (($_SERVER['PHP_SELF'][0] == '/') ? '' : '/') . $_SERVER['PHP_SELF'];

        return $server . $url;
    }

    protected function _parseUploadedFiles($files): array
    {
        $parser = new lmbUploadedFilesParser();
        return $parser->objectify($files);
    }

    protected function _stripHttpSlashes($data, $result = [])
    {
        foreach ($data as $k => $v) {
            if (is_array($v))
                $result[$k] = $this->_stripHttpSlashes($v);
            else
                $result[$k] = stripslashes($v);
        }
        return $result;
    }

    function hasFiles($key = null): bool
    {
        $has = $this->_get($this->__files, $key);

        return !empty($has);
    }

    function getFiles($key = null)
    {
        return $this->_get($this->__files, $key);
    }

    /** @return \limb\net\src\lmbUploadedFile|false */
    function getFile($name)
    {
        $file = $this->getFiles($name);
        if (is_object($file))
            return $file;

        return false;
    }

    /* @deprecated */
    function getRequest($key = null, $default = null)
    {
        $request = lmbArrayHelper::arrayMerge($this->__get, $this->__post, $this->__files);
        return $this->_get($request, $key, $default);
    }

    function getGet($key = null, $default = null)
    {
        return $this->_get($this->__get, $key, $default);
    }

    function getPost($key = null, $default = null)
    {
        return $this->_get($this->__post, $key, $default);
    }

    function hasPost(): bool
    {
        if ($this->__pretend_post)
            return true;

        return sizeof($this->__post) > 0 || $this->getMethod() == 'post';
    }

    public function getClientIp()
    {
        return lmbIp::getRealIp();
    }

    public function getHeaders()
    {
        return $this->__headers;
    }

    public function getHeader($name, $default_value = null)
    {
        return $this->__headers[$name] ?? $default_value;
    }

    public function hasHeader($name): bool
    {
        return isset($this->__headers[$name]);
    }

    function isAjax(): bool
    {
        if ($this->has('DNT'))
            return true;

        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
    }

    function isPjax(): bool
    {
        return isset($_SERVER['HTTP_X_PJAX']);
    }

    function pretendPost($flag = true)
    {
        $this->__pretend_post = $flag;
    }

    function getCookie($key = null, $default = null)
    {
        return $this->_get($this->__cookies, $key, $default);
    }

    function getSafe($var, $default = null)
    {
        return htmlspecialchars($this->get($var, $default));
    }

    function getFiltered($key, $filter, $default = null)
    {
        return filter_var($this->get($key, $default), $filter);
    }

    function getGetFiltered($key, $filter, $default = null)
    {
        $value = $this->getGet($key, $default);
        if (is_array($key))
            return filter_var_array($value, $filter);
        else
            return filter_var($value, $filter);
    }

    function getPostFiltered($key, $filter, $default = null)
    {
        $value = $this->getPost($key, $default);
        if (is_array($key))
            return filter_var_array($value, $filter);
        else
            return filter_var($value, $filter);
    }

    protected function _get($arr, $key = null, $default = null)
    {
        if (is_null($key))
            return $arr;

        if (is_array($key)) {
            $ret = array();
            foreach ($key as $item)
                $ret[$item] = ($arr[$item] ?? null);
            return $ret;
        } elseif (isset($arr[$key]))
            return $arr[$key];

        return $default;
    }

    function get($key, $default = null)
    {
        $_key = "__$key";
        if (in_array($_key, $this->__reserved_attrs))
            return $this->$_key;

        if (in_array($key, $this->__reserved_attrs))
            return null;

        if (isset($this->$key))
            return $this->$key;

        if (isset($this->__attributes[$key]))
            return $this->__attributes[$key]; // remove this if in 5.x

        return $default;
    }

    function set($key, $value)
    {
        $this->$key = $value;
    }

    function merge($data = [])
    {
        foreach ($data as $key => $value)
            $this->$key = $value;
    }

    function has($key): bool
    {
        return isset($this->$key) || isset($this->__attributes[$key]);
    }

    function export(): array
    {
        $exported = array();

        $object_vars = get_object_vars($this);
        foreach ($object_vars as $name => $var)
            $exported[$name] = $var;

        return $exported;
    }

    function getBoolean($name, $default = false)
    {
        return filter_var($this->get($name, $default), FILTER_VALIDATE_BOOLEAN);
    }

    function getUri(): lmbUri
    {
        return $this->__uri;
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        if ($uri === $this->getUri()) {
            return $this;
        }

        $new = clone($this);
        $new->__uri = $uri;
        $items = $new->__uri->getQueryItems();
        foreach ($items as $k => $v) {
            $new->__get[$k] = $v;

            if (in_array($k, $new->__reserved_attrs))
                continue;
            $new->set($k, $v);
        }

        if (!$preserveHost || !$this->hasHeader('Host')) {
            $new->updateHostFromUri();
        }

        return $new;
    }

    private function updateHostFromUri(): void
    {
        $host = $this->__uri->getHost();

        if ($host == '') {
            return;
        }

        if (($port = $this->__uri->getPort()) !== null) {
            $host .= ':' . $port;
        }

        /*if ($this->hasHeader('Host')) {
            $header = $this->getHeader('Host');
        } else {
            $header = 'Host';
        }*/
        $header = 'Host';

        $this->__headers = [$header => $host] + $this->__headers;
    }

    function toString(): string
    {
        $flat = array();
        $query = '';

        $data = lmbArrayHelper::arrayMerge($this->__get, $this->__post);
        lmbArrayHelper::toFlatArray($data, $flat);

        foreach ($flat as $key => $value) {
            if (is_object($value))
                continue;
            $query .= $key . '=' . urlencode($value) . '&';
        }

        $uri = $this->__uri->withoutQueryItems();

        return rtrim($uri->toString() . '?' . rtrim($query, '&'), '?');
    }

    function dump(): string
    {
        return $this->toString();
    }

    public function getProtocolVersion(): string
    {
        return $this->__version;
    }

    public function withProtocolVersion($version)
    {
        if ($this->__version === $version) {
            return $this;
        }

        $new = clone($this);
        $new->__version = $version;
        return $new;
    }

    public function getHeaderLine($name)
    {
        $header_name = strtolower($name);

        return isset($this->__headers[$header_name]) ? ($header_name . ': ' . $this->__headers[$header_name]) : '';
    }

    public function withHeader($name, $value)
    {
        if (isset($this->__headers[$name]) && $this->__headers[$name] === $value) {
            return $this;
        }

        $new = clone($this);
        $new->__headers[$name] = $value;
        return $new;
    }

    public function withAddedHeader($name, $value)
    {
        $normalized = strtolower($name);

        $new = clone($this);
        if(!isset($new->__headers[$normalized]))
            $new->__headers[$normalized] = [];

        $new->__headers[$normalized][] = $value;

        return $new;
    }

    public function withoutHeader($name)
    {
        $normalized = strtolower($name);

        $new = clone($this);
        unset($new->__headers[$normalized]);

        return $new;
    }

    public function getBody()
    {
        if (!$this->stream) {
            $this->stream = new lmbHttpStream(fopen('php://input', 'r'));
        }

        return $this->stream;
    }

    public function withBody(StreamInterface $body)
    {
        if ($body === $this->stream) {
            return $this;
        }

        $new = clone($this);
        //$new->stream = new lmbHttpStream($body);
        $new->stream = $body;

        return $new;
    }

    public function getRequestTarget()
    {
        if ($this->__requestTarget !== null) {
            return $this->__requestTarget;
        }
        $target = $this->getUri()
            ->getPath();
        if ($target == '') {
            $target = '/';
        }
        if ($this->getUri()
                ->getQuery() != '') {
            $target .= '?' . $this->getUri()
                    ->getQuery();
        }
        return $target;
    }

    public function withRequestTarget($requestTarget)
    {
        if ($this->__requestTarget === $requestTarget) {
            return $this;
        }

        $new = clone($this);
        $new->__requestTarget = $requestTarget;
        return $new;
    }

    public function getMethod()
    {
        return strtolower($this->__method);
    }

    public function withMethod($method)
    {
        if ($this->__method === $method) {
            return $this;
        }

        $new = clone($this);
        $new->__method = $method;
        return $new;
    }

    public function isMethod($method_name): bool
    {
        return strncasecmp($this->__method, $method_name, 4) === 0;
    }

    public function getAttributes()
    {
        return $this->__attributes;
    }

    public function getAttribute($name, $default = null)
    {
        return $this->__attributes[$name] ?? $default;
    }

    /** @deprecated  */
    public function setAttribute($name, $value)
    {
        $this->__attributes[$name] = $value;
    }

    public function withAttribute($name, $value)
    {
        if (isset($this->__attributes[$name]) && $this->__attributes[$name] === $value) {
            return $this;
        }

        $new = clone($this);
        $new->__attributes[$name] = $value;
        return $new;
    }

    public function withoutAttribute($name)
    {
        if (!isset($this->__attributes[$name])) {
            return $this;
        }

        $new = clone($this);
        unset($new->__attributes[$name]);
        return $new;
    }

    public function offsetExists(mixed $offset)
    {
        return $this->has($offset);
    }

    public function offsetGet(mixed $offset)
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset(mixed $offset)
    {
        unset($this->$offset);
    }
}
