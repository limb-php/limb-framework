<?php
namespace limb\cron\src\controller;

use limb\core\src\lmbEnv;
use limb\web_app\src\controller\lmbController;
use limb\cron\src\cron\CronJobLogger;
use limb\dbal\src\lmbTableGateway;
use limb\dbal\src\criteria\lmbSQLCriteria;

class AdminCronJobController extends lmbController
{
  protected function _getCronJobsNames()
  {
    $schedule_dir = lmbEnv::get('PROJECT_DIR') . "/cli/cron/";
    $schedule_files = array_filter(scandir($schedule_dir), array('AdminCronJobController', 'filter_schedule_files'));

    $jobs = array();
    foreach($schedule_files as $file)
    {
      $schedule_jobs = array_map(array('AdminCronJobController', 'filter_jobs_names'), explode("\n", file_get_contents($schedule_dir . $file)));
      foreach($schedule_jobs as $job_file_path)
      {
        if($job_file_path == '')
          continue;

        $parsed_path = CronJobLogger::parseCronJobFilePath($job_file_path);
        $jobs[$parsed_path['cron_job_name']] = $parsed_path['cron_job_name'];
      }
    }
    return $jobs;
  }

  function filter_schedule_files($var)
  {
    return preg_match("/^_/i", $var);
  }

  function filter_jobs_names($var)
  {
    return preg_replace("/.*cron_runner\s*(\S*).*/i", "\$1", $var);
  }

  function doDisplay()
  {
    $this->useForm('filter_form');
    $this->setFormDatasource($this->request);

    $this->job_names = $this->_getCronJobsNames();

    $criteria = new lmbSQLCriteria();

    $job_name = $this->request->get('job_name');
    if('' != $job_name)
      $criteria->addAnd(lmbSQLCriteria::equal('name', $job_name));

    if((bool)$this->request->get('only_failed'))
    {
      $criteria->addAnd(lmbSQLCriteria::in('status', array(
        CronJobLogger::STATUS_ERROR,
        CronJobLogger::STATUS_EXCEPTION,
        CronJobLogger::STATUS_CONFLICT,
      )));
    }

    $table = new lmbTableGateway(CronJobLogger::TABLE_NAME, $this->toolkit->getDefaultDbConnection());
    $this->items = $table->select($criteria, array('id' => 'DESC'));
  }
}
