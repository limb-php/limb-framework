<?php

namespace limb\net\src;

interface RedirectStrategy
{
    function redirect($response, $path);
}