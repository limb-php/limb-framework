<?php

namespace tests\active_record\cases\src;

use limb\core\src\lmbObject;

class ImageForAggregateTestObject extends lmbObject
{
    protected $extension;
    protected $photo_id;

    function getUrl()
    {
        return '/image_' . $this->photo_id . '.' . $this->image_extension;
    }
}
