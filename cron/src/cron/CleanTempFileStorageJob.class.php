<?php
namespace limb\cron\src\cron;

use limb\fs\src\lmbFs;
use limb\datetime\src\lmbDateTime;

class CleanTempFileStorageJob extends CronJob
{
  function run()
  {
    $temp_storage = lmbToolkit::instance()->getConf('common')->get('temp_file_storage');
    $temp_storage_path = $temp_storage['store_rules']['path'];

    $files = lmbFs::ls($temp_storage_path);

    foreach($files as $file)
      if(!is_dir($file))
      {
        $timestamp = filemtime($temp_storage_path . $file);
        if($timestamp < time() - $temp_storage['file_ttl'])
          lmbFs::rm($temp_storage_path . $file);
      }
  }
}
