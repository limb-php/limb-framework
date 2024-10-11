<?php

namespace Limb\Tests\ActiveRecord\Cases\src;

use limb\core\lmbObject;

class ImageForAggregateTest extends lmbObject
{
    protected $extension;
    protected $photo_id;

    function getUrl()
    {
        return '/image_' . $this->photo_id . '.' . $this->image_extension;
    }
}
