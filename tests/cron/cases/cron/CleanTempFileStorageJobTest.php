<?php

namespace Tests\cron\cases\cron;

use limb\cron\src\cron\CleanTempFileStorageJob;
use Tests\cron\cases\CronModuleTestCase;
use limb\toolkit\src\lmbToolkit;
use limb\fs\src\lmbFs;

class CleanTempFileStorageJobTest extends CronModuleTestCase
{
    function testRun()
    {
        $conf = lmbToolkit::instance()->getConf('common')->get('temp_file_storage');
        $temp_storage = $conf['store_rules']['path'];

        $cron_job = new CleanTempFileStorageJob();

        lmbFs::mkdir($temp_storage);

        $file1 = tempnam($temp_storage, 'f1');
        $file2 = tempnam($temp_storage, 'f2');
        $file3 = tempnam($temp_storage, 'f3');

        touch($file1, time() - 2 * $conf['file_ttl']);
        touch($file2, time() - 2 * $conf['file_ttl']);
        touch($file3, time() - 2 * $conf['file_ttl']);

        $file4 = tempnam($temp_storage, 'f4');
        $file5 = tempnam($temp_storage, 'f5');

        $cron_job->run();

        $this->assertFalse(file_exists($file1));
        $this->assertFalse(file_exists($file2));
        $this->assertFalse(file_exists($file3));

        $this->assertTrue(file_exists($file4));
        $this->assertTrue(file_exists($file5));

        touch($file4, time() - 2 * $conf['file_ttl']);
        touch($file5, time() - 2 * $conf['file_ttl']);

        $cron_job->run();

        $this->assertFalse(file_exists($file4));
        $this->assertFalse(file_exists($file5));
    }
}
