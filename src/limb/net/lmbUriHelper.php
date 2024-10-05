<?php

namespace limb\net;

use Psr\Http\Message\UriInterface;

class lmbUriHelper
{

    static function compare(lmbUri $uri, lmbUri $uri2)
    {
        return (
            $uri->getScheme() == $uri2->getScheme() &&
            $uri->getHost() == $uri2->getHost() &&
            $uri->getPort() == $uri2->getPort() &&
            $uri->getUser() === $uri2->getUser() &&
            $uri->getPassword() === $uri2->getPassword() &&
            self::compareQuery($uri, $uri2) &&
            self::comparePath($uri, $uri2) === 0
        );
    }

    static function compareQuery(lmbUri $uri, lmbUri $uri2)
    {
        if ($uri->countQueryItems() != $uri2->countQueryItems())
            return false;

        foreach ($uri->getQueryItems() as $name => $value) {
            if ((($item = $uri2->getQueryItem($name)) === false) ||
                $item != $value)
                return false;
        }
        return true;
    }

    static function comparePath(lmbUri $uri, lmbUri $uri2)
    {
        $count1 = $uri->countPath();
        $count2 = $uri2->countPath();
        $iterCount = min($count1, $count2);

        for ($i = 0; $i < $iterCount; $i++) {
            if (self::getPathElement($uri, $i) != self::getPathElement($uri2, $i))
                return false;
        }

        return ($count1 - $count2);
    }


    static function getPathElements(UriInterface $uri): array
    {
        return explode('/', $uri->getPath());
    }

    static function getPathElement(UriInterface $uri, $level): string
    {
        $path_elements = self::getPathElements($uri);

        return $path_elements[$level] ?? '';
    }

    static function getPathToLevel(UriInterface $uri, $level): string
    {
        $path_elements = self::getPathElements($uri);

        if (!$path_elements || $level >= sizeof($path_elements))
            return '';

        $items = array();
        for ($i = 0; $i <= $level; $i++)
            $items[] = $path_elements[$i];

        return implode('/', $items);
    }

    static function getPathFromLevel(UriInterface $uri, $level)
    {
        $path_elements = self::getPathElements($uri);

        if ($level <= 0)
            return $uri->getPath();

        if (!$path_elements || $level >= sizeof($path_elements))
            return '/';

        $items[] = '';

        for ($i = $level; $i < sizeof($path_elements); $i++)
            $items[] = $path_elements[$i];

        return implode('/', $items);
    }

    /**
     * Resolves //, ../ and ./ from a path and returns
     * the result. Eg:
     *
     * /foo/bar/../boo.php    => /foo/boo.php
     * /foo/bar/../../boo.php => /boo.php
     * /foo/bar/.././/boo.php => /foo/boo.php
     *
     */
    static function normalizePath(string $path): string
    {
        $path = explode('/', preg_replace('~[\/]+~', '/', $path));

        for ($i = 0; $i < sizeof($path); $i++) {
            if ($path[$i] == '.') {
                unset($path[$i]);
                $path = array_values($path);
                $i--;
            } elseif ($path[$i] == '..' && ($i > 1 || ($i == 1 && $path[0] != ''))) {
                unset($path[$i]);
                unset($path[$i - 1]);
                $path = array_values($path);
                $i -= 2;
            } elseif ($path[$i] == '..' && $i == 1 && $path[0] == '') {
                unset($path[$i]);
                $path = array_values($path);
                $i--;
            } else
                continue;
        }

        return implode('/', $path);
    }
}