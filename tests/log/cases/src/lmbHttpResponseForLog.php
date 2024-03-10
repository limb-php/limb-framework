<?php

namespace tests\log\cases\src;

use limb\net\src\lmbHttpResponse;

class lmbHttpResponseForLog extends lmbHttpResponse
{
    function getHeaders()
    {
        return $this->headers;
    }
}
