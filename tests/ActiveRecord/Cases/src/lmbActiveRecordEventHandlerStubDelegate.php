<?php

namespace Limb\Tests\ActiveRecord\Cases\src;

use limb\active_record\lmbActiveRecord;

class lmbActiveRecordEventHandlerStubDelegate
{
    var $calls_order = '';

    function onBeforeSave($active_record)
    {
        if ($active_record instanceof lmbActiveRecord)
            $this->calls_order .= '|onBeforeSave ' . get_class($active_record) . '|';
    }

    function onAfterSave($active_record)
    {
        if ($active_record instanceof lmbActiveRecord)
            $this->calls_order .= '|onAfterSave ' . get_class($active_record) . '|';
    }

    function onBeforeUpdate($active_record)
    {
        if ($active_record instanceof lmbActiveRecord)
            $this->calls_order .= '|onBeforeUpdate ' . get_class($active_record) . '|';
    }

    function onUpdate($active_record)
    {
        if ($active_record instanceof lmbActiveRecord)
            $this->calls_order .= '|onUpdate ' . get_class($active_record) . '|';
    }

    function onAfterUpdate($active_record)
    {
        if ($active_record instanceof lmbActiveRecord)
            $this->calls_order .= '|onAfterUpdate ' . get_class($active_record) . '|';
    }

    function onBeforeCreate($active_record)
    {
        if ($active_record instanceof lmbActiveRecord)
            $this->calls_order .= '|onBeforeCreate ' . get_class($active_record) . '|';
    }

    function onCreate($active_record)
    {
        if ($active_record instanceof lmbActiveRecord)
            $this->calls_order .= '|onCreate ' . get_class($active_record) . '|';
    }

    function onAfterCreate($active_record)
    {
        if ($active_record instanceof lmbActiveRecord)
            $this->calls_order .= '|onAfterCreate ' . get_class($active_record) . '|';
    }

    function onBeforeDestroy($active_record)
    {
        if ($active_record instanceof lmbActiveRecord)
            $this->calls_order .= '|onBeforeDestroy ' . get_class($active_record) . '|';
    }

    function onAfterDestroy($active_record)
    {
        if ($active_record instanceof lmbActiveRecord)
            $this->calls_order .= '|onAfterDestroy ' . get_class($active_record) . '|';
    }

    function getCallsOrder()
    {
        return $this->calls_order;
    }

    function subscribeForEvents($active_record)
    {
        $active_record->registerOnBeforeSaveCallback($this, 'onBeforeSave');
        $active_record->registerOnAfterSaveCallback($this, 'onAfterSave');
        $active_record->registerOnBeforeUpdateCallback($this, 'onBeforeUpdate');
        $active_record->registerOnUpdateCallback($this, 'onUpdate');
        $active_record->registerOnAfterUpdateCallback($this, 'onAfterUpdate');
        $active_record->registerOnBeforeCreateCallback($this, 'onBeforeCreate');
        $active_record->registerOnCreateCallback($this, 'onCreate');
        $active_record->registerOnAfterCreateCallback($this, 'onAfterCreate');
        $active_record->registerOnBeforeDestroyCallback($this, 'onBeforeDestroy');
        $active_record->registerOnAfterDestroyCallback($this, 'onAfterDestroy');
    }

    function subscribeGloballyForEvents()
    {
        lmbActiveRecord::registerGlobalOnBeforeSaveCallback($this, 'onBeforeSave');
        lmbActiveRecord::registerGlobalOnAfterSaveCallback($this, 'onAfterSave');
        lmbActiveRecord::registerGlobalOnBeforeUpdateCallback($this, 'onBeforeUpdate');
        lmbActiveRecord::registerGlobalOnUpdateCallback($this, 'onUpdate');
        lmbActiveRecord::registerGlobalOnAfterUpdateCallback($this, 'onAfterUpdate');
        lmbActiveRecord::registerGlobalOnBeforeCreateCallback($this, 'onBeforeCreate');
        lmbActiveRecord::registerGlobalOnCreateCallback($this, 'onCreate');
        lmbActiveRecord::registerGlobalOnAfterCreateCallback($this, 'onAfterCreate');
        lmbActiveRecord::registerGlobalOnBeforeDestroyCallback($this, 'onBeforeDestroy');
        lmbActiveRecord::registerGlobalOnAfterDestroyCallback($this, 'onAfterDestroy');
    }
}
