<?php

namespace tests\active_record\cases\src;

use limb\active_record\src\lmbActiveRecord;

class PhotoForTest extends lmbActiveRecord
{
    protected $_composed_of = array('image' => array('class' => ImageForAggregateTest::class,
        'mapping' => array('photo_id' => 'id',
            'extension' => 'image_extension')),

        'extra' => array('class' => ExtraForAggregateTest::class));
}
