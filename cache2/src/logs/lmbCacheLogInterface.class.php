<?php
interface lmbCacheLogInterface
{
  static function instance();

  function addRecord($key, $operation, $time, $result);
  function getStatistic();
  function getRecords();

}
