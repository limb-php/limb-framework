<?php

namespace Limb\Log;

use Limb\Net\lmbUri;

class lmbLogFirePHPWriter extends \FB implements lmbLogWriterInterface
{
    protected $check_client_extension;

    function __construct(lmbUri $dsn)
    {
        $this->check_client_extension = (bool)$dsn->getQueryItem('check_extension', true);
    }

    function write(lmbLogEntry $entry)
    {
        return $this->log($entry->asText());
    }

    function disableCheckClientExtension()
    {
        $this->check_client_extension = false;
    }

    function isClientExtensionCheckEnabled()
    {
        return $this->check_client_extension;
    }
}
