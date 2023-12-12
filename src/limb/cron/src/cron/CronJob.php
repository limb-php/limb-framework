<?php

namespace limb\cron\src\cron;

abstract class CronJob
{
    abstract function run();
}
