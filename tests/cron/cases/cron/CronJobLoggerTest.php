<?php
namespace tests\cron\cases\cron;

use limb\cron\src\cron\CronJobLogger;
use tests\cron\cases\CronModuleTestCase;

class CronJobLoggerTest extends CronModuleTestCase
{

  protected $tables_to_cleanup = array('cron_job_log');

  function testMakeStartRecord_WithoutOutput()
  {
    $this->cron_job_logger->makeStartRecord();
    $rs = $this->db_table->select()->getArray();
    $rs = $rs[0];

    $this->assertEquals('TestCron', $rs['name']);
    $this->assertEquals(CronJobLogger::STATUS_START, $rs['status']);
    $this->assertEquals('', $rs['info']);
  }

  function testMakeStartRecord_WithOutput()
  {
    $output_message = 'some output';

    $this->cron_job_logger->makeStartRecord($output_message);
    $rs = $this->db_table->select()->getArray();
    $rs = $rs[0];

    $this->assertEquals('TestCron', $rs['name']);
    $this->assertEquals(CronJobLogger::STATUS_START, $rs['status']);
    $this->assertEquals($rs['info'], $output_message);
  }

  function testMakeConflictRecord_WithoutOutput()
  {
    $this->cron_job_logger->makeConflictRecord();

    $rs = $this->db_table->select()->getArray();
    $rs = $rs[0];

    $this->assertEquals('TestCron', $rs['name']);
    $this->assertEquals(CronJobLogger::STATUS_CONFLICT, $rs['status']);
    $this->assertEquals('', $rs['info']);
  }

  function testMakeConflictRecord_WithOutput()
  {
    $output_message = 'some output';

    $this->cron_job_logger->makeConflictRecord($output_message);

    $rs = $this->db_table->select()->getArray();
    $rs = $rs[0];

    $this->assertEquals('TestCron', $rs['name']);
    $this->assertEquals(CronJobLogger::STATUS_CONFLICT, $rs['status']);
    $this->assertEquals($rs['info'], $output_message);
  }

  function testMakeEndRecord_SuccessStatus()
  {
    $this->cron_job_logger->makeEndRecord($error = null);

    $rs = $this->db_table->select()->getArray();
    $rs = $rs[0];

    $this->assertEquals('TestCron', $rs['name']);
    $this->assertEquals(CronJobLogger::STATUS_SUCCESS, $rs['status']);
    $this->assertEquals('', $rs['info']);
  }

  function testMakeEndRecord_ErrorStatusWithoutOutput()
  {
    $error_message = 'some error';

    $this->cron_job_logger->makeEndRecord($error_message);

    $rs = $this->db_table->select()->getArray();
    $rs = $rs[0];

    $this->assertEquals('TestCron', $rs['name']);
    $this->assertEquals(CronJobLogger::STATUS_ERROR, $rs['status']);
    $this->assertEquals($rs['info'], $error_message);
  }

  function testMakeEndRecord_ErrorStatusWithOutput()
  {
    $error_message = 'some error';
    $output_message = 'some output';

    $this->cron_job_logger->makeEndRecord($error_message, $output_message);

    $rs = $this->db_table->select()->getArray();
    $rs = $rs[0];

    $this->assertEquals('TestCron', $rs['name']);
    $this->assertEquals(CronJobLogger::STATUS_ERROR, $rs['status']);
    $this->assertEquals($rs['info'], $error_message . PHP_EOL . $output_message);
  }

  function testMakeEndRecord_ErrorStatus_ErrorIsArray()
  {
    $error = array('type' => 'DATABASE ERROR', 'error' => 'some error');

    $this->cron_job_logger->makeEndRecord($error);

    $rs = $this->db_table->select()->getArray();
    $rs = $rs[0];

    $expected_info = <<< EOD
array (
  'type' => 'DATABASE ERROR',
  'error' => 'some error',
)
EOD;

    $this->assertEquals('TestCron', $rs['name']);
    $this->assertEquals(CronJobLogger::STATUS_ERROR, $rs['status']);
    $this->assertEquals($rs['info'], $expected_info);
  }

  function testMakeExceptionRecord()
  {
    $exception_message = 'some exception';

    $this->cron_job_logger->makeExceptionRecord($exception_message);

    $rs = $this->db_table->select()->getArray();
    $rs = $rs[0];

    $this->assertEquals('TestCron', $rs['name']);
    $this->assertEquals(CronJobLogger::STATUS_EXCEPTION, $rs['status']);
    $this->assertEquals($rs['info'], $exception_message);
  }

  function testGetRecords_WithoutCount()
  {
    $this->cron_job_logger->makeStartRecord();
    $this->cron_job_logger->makeEndRecord($error = null);
    $this->cron_job_logger->makeExceptionRecord('some exception');

    $rs = $this->cron_job_logger->getRecords()->getArray();

    $this->assertEquals(CronJobLogger::STATUS_EXCEPTION, $rs[0]['status']);
    $this->assertEquals(CronJobLogger::STATUS_SUCCESS, $rs[1]['status']);
    $this->assertEquals(CronJobLogger::STATUS_START, $rs[2]['status']);


    $this->assertCount(3, $rs);
  }

  function testGetRecords_WithCount()
  {
    $this->cron_job_logger->makeStartRecord();
    $this->cron_job_logger->makeEndRecord($error = null);
    $this->cron_job_logger->makeExceptionRecord('some exception');
    $this->cron_job_logger->makeStartRecord();
    $this->cron_job_logger->makeStartRecord();

    $rs = $this->cron_job_logger->getRecords($count = 2)->getArray();

    $this->assertEquals(2, count($rs));
  }
}
