<?php

namespace Limb\Tests\Log\Cases\src;

use limb\net\lmbHttpResponse;

class lmbHttpResponseForLog extends lmbHttpResponse
{
    function getHeaders(): array
    {
        return $this->headers;
    }
}
