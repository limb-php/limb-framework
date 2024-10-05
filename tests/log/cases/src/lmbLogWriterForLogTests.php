<?php

namespace tests\log\cases\src;

use limb\log\lmbLogEntry;
use limb\log\lmbLogWriterInterface;
use limb\net\lmbUri;

class lmbLogWriterForLogTests implements lmbLogWriterInterface
{

    protected $entry;

    function __construct(lmbUri $dsn)
    {
    }

    function write(lmbLogEntry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * @return lmbLogEntry
     */
    function getWritten()
    {
        return $this->entry;
    }
}