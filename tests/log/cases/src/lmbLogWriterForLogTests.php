<?php

namespace tests\log\cases\src;

use limb\log\src\lmbLogEntry;
use limb\log\src\lmbLogWriterInterface;
use limb\net\src\lmbUri;

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