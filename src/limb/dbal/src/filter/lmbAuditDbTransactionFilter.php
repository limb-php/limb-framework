<?php
namespace limb\dbal\src\filter;

use limb\core\src\lmbEnv;
use limb\filter_chain\src\lmbInterceptingFilterInterface;
use limb\dbal\src\drivers\lmbAuditDbConnection;
use limb\toolkit\src\lmbToolkit;

class lmbAuditDbTransactionFilter implements lmbInterceptingFilterInterface
{
  function run($filter_chain)
  {
    $toolkit = lmbToolkit::instance();

    if( 'devel' !== lmbEnv::get('LIMB_APP_MODE') )
    {
      $filter_chain->next();
      return;
    }

    $conn = new lmbAuditDbConnection( $toolkit->getDefaultDbConnection() );
    $toolkit->setDefaultDbConnection($conn);

    $filter_chain->next();

    $this->_printStat( $toolkit->getResponse(), $conn->getStats() );
  }

  protected function _printStat($response, $info_arr)
  {
    if( $response->getContentType() != 'text/html' )
        return;

    $output = "<!--";
    $total_time = 0;
    $i = 1;
    foreach($info_arr as $info)
    {
      $result = array('num: ' . $i++, 'query: ' . $info['query'], 'time: ' . $info['time'] . "\n");
      $output .= implode("\n", $result);

      $total_time += $info['time'];
    }
    $output .= "Total Time: " . $total_time . " -->";

    $response->append($output);
  }
}

