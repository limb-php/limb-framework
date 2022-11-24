<?php
lmb_require('limb/filter_chain/src/lmbInterceptingFilter.interface.php');
lmb_require('limb/dbal/src/drivers/lmbAuditDbConnection.class.php');

class lmbAuditDbTransactionFilter implements lmbInterceptingFilter
{
  protected $toolkit;

  function run($filter_chain)
  {
    $this->toolkit = lmbToolkit :: instance();

    if( !$this->toolkit->isWebAppDebugEnabled() )
    {
      $filter_chain->next();
      return;
    }

    $conn = new lmbAuditDbConnection( $this->toolkit->getDefaultDbConnection() );
    $this->toolkit->setDefaultDbConnection($conn);

    $filter_chain->next();

    if( $this->toolkit->getResponse()->getContentType() == 'text/html' )
      $this->_printStat( $conn->getStats() );
  }

  protected function _printStat($info_arr)
  {
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

    $this->toolkit->getResponse()->append($output);
  }
}

