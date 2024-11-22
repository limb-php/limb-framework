<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\search\src\indexer;

/**
 * interface lmbSearchIndexerInterface.
 *
 * @package search
 * @version $Id: lmbSearchIndexerInterface.php 7686 2009-03-04 19:57:12Z
 */
interface lmbSearchIndexerInterface
{
    function index($uri, $content);
}
