<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_spider\src;

/**
 * class lmbUriFilter.
 *
 * @package web_spider
 * @version $Id: lmbUriFilter.php 7903 2009-04-26 18:36:36Z
 */

use Psr\Http\Message\UriInterface;

class lmbUriFilter
{
    protected $allowed_protocols = [];
    protected $allowed_hosts = [];

    protected $allowed_path_regexes = [];
    protected $disallowed_path_regexes = [];

    function allowProtocol($protocol)
    {
        $this->allowed_protocols[] = strtolower($protocol);
    }

    function allowHost($host)
    {
        $this->allowed_hosts[] = strtolower($host);
    }

    function allowPathRegex($regex)
    {
        $this->allowed_path_regexes[] = $regex;
    }

    function disallowPathRegex($regex)
    {
        $this->disallowed_path_regexes[] = $regex;
    }

    function canPass(UriInterface $uri): bool
    {
        if (!in_array($uri->getScheme(), $this->allowed_protocols))
            return false;

        if (!in_array($uri->getHost(), $this->allowed_hosts))
            return false;

        if (!sizeof($this->allowed_path_regexes))
            return false;

        foreach ($this->disallowed_path_regexes as $regex) {
            if (preg_match($regex, $uri->toString(['path', 'query', 'anchor'])))
                return false;
        }

        foreach ($this->allowed_path_regexes as $regex) {
            if (!preg_match($regex, $uri->toString(['path', 'query', 'anchor'])))
                return false;
        }

        return true;
    }
}
