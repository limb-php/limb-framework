<?php

namespace tests\active_record\cases\src;

class TestOneTableObjectWithHooks extends TestOneTableObject
{
    protected function _onValidate()
    {
        echo '|on_validate|';
    }

    protected function _onBeforeUpdate()
    {
        echo '|on_before_update|';
    }

    protected function _onBeforeCreate()
    {
        echo '|on_before_create|';
    }

    protected function _onBeforeSave()
    {
        echo '|on_before_save|';
    }

    protected function _onAfterSave()
    {
        echo '|on_after_save|';
    }

    protected function _onSave()
    {
        echo '|on_save|';
    }

    protected function _onUpdate()
    {
        echo '|on_update|';
    }

    protected function _onCreate()
    {
        echo '|on_create|';
    }

    protected function _onAfterUpdate()
    {
        echo '|on_after_update|';
    }

    protected function _onAfterCreate()
    {
        echo '|on_after_create|';
    }

    protected function _onBeforeDestroy()
    {
        echo '|on_before_destroy|';
    }

    protected function _onAfterDestroy()
    {
        echo '|on_after_destroy|';
    }
}
