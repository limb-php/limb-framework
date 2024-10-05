<?php

namespace tests\log\cases\src;

use limb\net\lmbHttpResponse;

class lmbHttpResponseForLog extends lmbHttpResponse
{
    function getHeaders(): array
    {
        return $this->headers;
    }
}
