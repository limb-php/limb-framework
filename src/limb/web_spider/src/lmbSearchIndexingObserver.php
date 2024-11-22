<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_spider\src;

/**
 * class lmbSearchIndexingObserver.
 *
 * @package web_spider
 * @version $Id: lmbSearchIndexingObserver.php 7812 2009-03-24 14:05:00Z
 */
class lmbSearchIndexingObserver
{
    protected $counter = 0;
    protected $indexer;

    function __construct($indexer)
    {
        $this->indexer = $indexer;
    }

    function notify($reader)
    {
        $uri = $reader->getUri();

        $this->counter++;

        echo "{$this->counter})started indexing " . $uri->toString() . "...";

        $this->indexer->index($uri, $reader->getContent());

        echo "done\n";
    }
}
