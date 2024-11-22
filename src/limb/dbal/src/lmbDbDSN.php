<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src;

use limb\core\src\lmbObject;
use limb\net\src\lmbUri;
use limb\core\src\exception\lmbException;

/**
 * class lmbDbDSN.
 *
 * @package dbal
 * @version $Id: lmbDbDSN.php 8069 2010-01-20 08:16:38Z
 */
class lmbDbDSN extends lmbObject
{
    protected $uri;

    protected $driver;
    protected $scheme;
    protected $host;
    protected $user;
    protected $password;
    protected $database;
    protected $port;
    protected $charset;
    protected $reconnect = false;
    protected $extra = [];

    function __construct($args)
    {
        if (is_array($args)) {
            foreach ($args as $key => $value) {
                if (is_numeric($key) && is_array($value))
                    $this->extra = $value;
                else
                    $this->$key = $value;
            }
        } elseif (is_string($args))
            $this->_parseUri($args);

        foreach ($this->extra as $key => $value)
            $this->$key = $value;
    }

    function _parseUri($str)
    {
        try {
            $this->uri = new lmbUri($str);
        } catch (lmbException $e) {
            throw new lmbException("Database DSN '$str' is not valid");
        }

        $this->driver = $this->uri->getQueryItem('driver');
        if(!$this->driver)
            $this->driver = $this->uri->getScheme();
        $this->scheme = $this->uri->getScheme();
        $this->host = $this->uri->getHost();
        $this->user = $this->uri->getUser();
        $this->password = $this->uri->getPassword();
        $this->database = substr($this->uri->getPath(), 1);//removing only first slash
        $this->port = $this->uri->getPort();
        $this->extra = $this->uri->getQueryItems();
    }

    function _getUri()
    {
        if (!is_object($this->uri))
            $this->uri = $this->buildUri();

        return $this->uri;
    }

    function buildUri()
    {
        $uri = (new lmbUri())
            ->withScheme($this->scheme ?? $this->driver)
            ->withHost($this->host)
            ->withUserInfo($this->get('user', ''), $this->get('password', ''))
            ->withPath('/' . $this->get('database', ''))
            ->withPort($this->port ?? null)
            ->withQueryItems($this->extra);

        if($this->scheme && $this->driver)
            $uri = $uri->withQueryItem('driver', $this->driver);

        return $uri;
    }

    function toString()
    {
        return $this->_getUri()->toString();
    }

    public function toArray(): array
    {
        return [
            'driver' => $this->driver,
            'scheme' => $this->scheme,
            'host' => $this->host,
            'user' => $this->user,
            'password' =>  $this->password,
            'port' =>  $this->port,
            'charset' =>  $this->charset,
            'database' =>  $this->database,
            'reconnect' =>  $this->reconnect,
            'extra' =>  $this->extra
        ];
    }
}
