<?php
namespace limb\cron\src\cron;

use limb\dbal\src\criteria\lmbSQLCriteria;
use limb\toolkit\src\lmbToolkit;
use limb\dbal\src\lmbTableGateway;

class CronJobLogger
{
  const TABLE_NAME = 'cron_job_log';

  const STATUS_START = 'START';
  const STATUS_SUCCESS = 'SUCCESS';
  const STATUS_ERROR = 'ERROR';
  const STATUS_EXCEPTION = 'EXCEPTION';
  const STATUS_CONFLICT = 'CONFLICT';

  protected $cron_job_name;
  protected $cron_job_file_path;

  protected $conn;

  function __construct($cron_job_file_path)
  {
    $this->cron_job_file_path = $cron_job_file_path;

    $parsed_path = self::parseCronJobFilePath($cron_job_file_path);
    $this->cron_job_name = $parsed_path['cron_job_name'];

    $this->conn = lmbToolkit::instance()->getDefaultDbConnection();
  }

  static function parseCronJobFilePath($cron_job_file_path)
  {
    $result['cron_job_file_path'] = $cron_job_file_path;
    $result['cron_job_file_name'] = substr($cron_job_file_path, strrpos($cron_job_file_path, '/') + 1);
    $exploded_file_name = explode('.', $result['cron_job_file_name']);
    $result['cron_job_class'] = $exploded_file_name[0];
    $result['cron_job_name'] = substr($result['cron_job_class'], 0, strpos($result['cron_job_class'], 'Job'));

    return $result;
  }

  protected function _getTable()
  {
    return new lmbTableGateway(self::TABLE_NAME, $this->conn);
  }

  protected function _makeRecord($status, $info = '')
  {
    $record  = array(
      'name'    => $this->cron_job_name,
      'time'    => time(),
      'status'  => $status,
      'info'    => $info,
      'path'    => $this->cron_job_file_path
    );
    $table  = $this->_getTable();
    $table->insert($record);
  }

  function getRecords($count = false)
  {
    $table = $this->_getTable();
    $rs = $table->select(new lmbSQLCriteria('name = \''.$this->cron_job_name . '\''), array('id' => 'DESC'));
    if($count)
      $rs->paginate(0, $count);
    return $rs;
  }

  function makeStartRecord($output = '')
  {
    $this->_makeRecord(self::STATUS_START, $output);
  }

  function makeConflictRecord($output = '')
  {
    $this->_makeRecord(self::STATUS_CONFLICT, $output);
  }

  function makeEndRecord($error, $output = '')
  {

    if(null === $error)
      $this->_makeRecord(self::STATUS_SUCCESS);
    else
    {
      if(!is_string($error))
        $error = var_export($error, true);

      if($output)
        $error .= PHP_EOL . $output;

      $this->_makeRecord(self::STATUS_ERROR, $error);
    }

    $this->is_ended = true;
  }

  function makeExceptionRecord($info)
  {
    $this->_makeRecord(self::STATUS_EXCEPTION, $info);
  }
}
