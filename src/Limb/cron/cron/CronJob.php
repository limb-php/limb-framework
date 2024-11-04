<?php

namespace limb\cron\cron;

abstract class CronJob
{
    abstract function run();
}
