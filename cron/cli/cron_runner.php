<?php

require_once(dirname(__FILE__) . '/../../../../setup.php');
lmb_require('limb/core/src/lmbBacktrace.class.php');
lmb_require('limb/core/src/lmbCollection.class.php');
lmb_require('limb/fs/src/lmbFS.class.php');
lmb_require('bit-cms/cron/src/cron/CronJobLogger.class.php');
/** Превед багу */
new lmbBacktrace;

function write_error_in_log($errno, $errstr, $errfile, $errline)
{
  global $logger;
  $back_trace = new lmbBacktrace(10, 10);
  $error_str = "error: $errstr\nfile: $errfile\nline: $errline\nbacktrace:".$back_trace->toString();
  $logger->makeEndRecord($error_str);
}

$debug_mode = false;
if(in_array('-d', $argv))
  $debug_mode = true;

//set_error_handler('write_error_in_log');
error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('memory_limit', -1);

if($argc < 2)
  die('Usage: cron_runner cron_job_file_path(starting from include_file_path)' . PHP_EOL);

$cron_job_file_path = $argv[1];
$parsed_cron_job_path = CronJobLogger :: parseCronJobFilePath($cron_job_file_path);
$logger = new CronJobLogger($parsed_cron_job_path['cron_job_file_path']);

$lock_dir = $_ENV['LIMB_VAR_DIR'] . '/cron_job_lock/';
if(!file_exists($lock_dir))
  lmbFS :: mkdir($lock_dir, 0777);

$lock_file = $lock_dir . $parsed_cron_job_path['cron_job_name'];
if(!file_exists($lock_file))
{
  file_put_contents($lock_file, '');
  chmod($lock_file, 0777);
}

$fp = fopen($lock_file, 'w');

if(!flock($fp, LOCK_EX + LOCK_NB))
{
  $logger->makeConflictRecord();
  return;
}

flock($fp, LOCK_EX + LOCK_NB);

  try {
    lmb_require($parsed_cron_job_path['cron_job_file_path']);
    $job  = new $parsed_cron_job_path['cron_job_class'];

    if(!in_array('-ld', $argv))
      $logger->makeStartRecord();

    ob_start();
      echo $parsed_cron_job_path['cron_job_class'] . ' started' . PHP_EOL;
      $result = $job->run();
      $output = ob_get_contents();
    ob_end_clean();

    if(!in_array('-ld', $argv))
      $logger->makeEndRecord($result, $output);
  }
  catch (lmbException $e)
  {
    $logger->makeExceptionRecord($e->getNiceTraceAsString());
    throw $e;
  }

flock($fp, LOCK_UN);
fclose($fp);

if(in_array('-v', $argv))
{
  echo $output;
  var_dump(lmbCollection :: toFlatArray($logger->getRecords(10)));
}
