<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_spider;

use limb\net\lmbUri;
use limb\core\exception\lmbException;
use limb\net\lmbUriHelper;
use Psr\Http\Message\UriInterface;

/**
 * class lmbWebSpider.
 *
 * @package web_spider
 * @version $Id: lmbWebSpider.php 7686 2009-03-04 19:57:12Z
 */
class lmbWebSpider
{
    protected $uri_extractor;
    protected $content_reader;
    protected $uri_filter;
    protected $meta_filter;
    protected $uri_normalizer;
    protected $content_type_filter;

    protected $observers = [];

    protected $uri_cache = [];

    function crawl(UriInterface $uri): bool
    {
        if ($uri->getHost() === '')
            return false;

        $this->_crawlRecursive($uri, $uri);

        return true;
    }

    function _crawlRecursive(UriInterface $uri, UriInterface $context_uri)
    {
        $uri = $this->_normalizeUriUsingContext($uri, $context_uri);

        $uri = $this->getUriNormalizer()->process($uri);

        if ($this->_isCacheHit($uri))
            return;

        $this->_markCached($uri);

        if (!$this->getUriFilter()->canPass($uri))
            return;

        $reader = $this->getUriContentReader();
        $reader->open($uri);

        if (!$this->getContentTypeFilter()->canPass($reader->getContentType()))
            return;

//        if (!$this->getMetaFilter()->canPass($reader->getContent()))
//            return;

        $this->_notifyObservers();

        $links = $this->getUriExtractor()->extract($reader->getContent());

        foreach (array_keys($links) as $key) {
            try {
                $link = $links[$key];
                if (!is_object($link))
                    $link = new lmbUri($link);

                $this->_crawlRecursive($link, $uri);
            } catch (lmbException $e) {

            }
        }
    }

    function _normalizeUriUsingContext(UriInterface $uri, UriInterface $context_uri): UriInterface
    {
        if (!$uri->getHost()) {
            $uri = $uri->withHost($context_uri->getHost());

            if (($path = $context_uri->getPath()) && $uri->isRelative()) {
                $path = preg_replace('~(.*)(/[^/]*)$~', '$1/', $path);
                $uri = $uri->withPath($path . $uri->getPath());
            }
        }

        if (!$uri->getScheme())
            $uri = $uri->withScheme($context_uri->getScheme());

        return $uri
            ->withFragment('')
            ->withPath( lmbUriHelper::normalizePath($uri->getPath()) );
    }

    function _isCacheHit($uri): bool
    {
        return isset($this->uri_cache[$uri->__toString()]);
    }

    function _markCached($uri): void
    {
        $this->uri_cache[$uri->__toString()] = 1;
    }

    function _notifyObservers()
    {
        foreach (array_keys($this->observers) as $key)
            $this->observers[$key]->notify($this->content_reader);
    }

    function registerObserver($observer)
    {
        $this->observers[] = $observer;
    }

    function getUriExtractor(): lmbUriExtractor
    {
        if ($this->uri_extractor)
            return $this->uri_extractor;

        //$this->uri_extractor = new lmbUriExtractor();
        return $this->uri_extractor;
    }

    function setUriExtractor($extractor): void
    {
        $this->uri_extractor = $extractor;
    }

    function getUriContentReader(): lmbUriContentReader
    {
        if ($this->content_reader)
            return $this->content_reader;

        //$this->content_reader = new lmbUriContentReader();
        return $this->content_reader;
    }

    function setUriContentReader($reader): void
    {
        $this->content_reader = $reader;
    }

    function getMetaFilter(): lmbMetaFilter
    {
        if ($this->meta_filter)
            return $this->meta_filter;

        //$this->meta_filter = new lmbMetaFilter();
        return $this->meta_filter;
    }

    function setMetaFilter($filter): void
    {
        $this->meta_filter = $filter;
    }

    function getContentTypeFilter(): lmbContentTypeFilter
    {
        if ($this->content_type_filter)
            return $this->content_type_filter;

        //$this->content_type_filter = new lmbContentTypeFilter();
        return $this->content_type_filter;
    }

    function setContentTypeFilter($filter): void
    {
        $this->content_type_filter = $filter;
    }

    function setUriFilter($filter): void
    {
        $this->uri_filter = $filter;
    }

    function getUriFilter(): lmbUriFilter
    {
        if ($this->uri_filter)
            return $this->uri_filter;

        //$this->uri_filter = new lmbUriFilter();
        return $this->uri_filter;
    }

    function setUriNormalizer($normalizer): void
    {
        $this->uri_normalizer = $normalizer;
    }

    function getUriNormalizer(): lmbUriNormalizer
    {
        if ($this->uri_normalizer)
            return $this->uri_normalizer;

        //$this->uri_normalizer = new lmbUriNormalizer();
        return $this->uri_normalizer;
    }
}
