<?php

namespace Tests\active_record\cases\src;

use limb\core\src\lmbCollectionDecorator;

class lmbARTestingDSDecorator extends lmbCollectionDecorator
{
    protected $value;

    function setValue($value)
    {
        $this->value = $value;
    }

    protected function _processRecord($record)
    {
        $record->set('value', $this->value);
    }

    function current()
    {
        $record = parent::current();
        $this->_processRecord($record);
        return $record;
    }

    function at($pos)
    {
        $record = parent::at($pos);
        $this->_processRecord($record);
        return $record;
    }
}
