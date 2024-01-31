<?php

namespace limb\dbal\src\filter;

use limb\core\src\lmbEnv;
use limb\datetime\src\lmbDateTime;
use limb\filter_chain\src\lmbInterceptingFilterInterface;
use limb\dbal\src\drivers\lmbAuditDbConnection;
use limb\log\src\lmbLog;
use limb\log\src\lmbLogPlainFileWriter;
use limb\toolkit\src\lmbToolkit;

class lmbAuditDbTransactionFilter implements lmbInterceptingFilterInterface
{
    function run($filter_chain, $request = null, $callback = null)
    {
        $toolkit = lmbToolkit::instance();

        if ('devel' !== lmbEnv::get('LIMB_APP_MODE')) {
            return $filter_chain->next($request, $callback);
        }

        $conn = new lmbAuditDbConnection($toolkit->getDefaultDbConnection());
        $toolkit->setDefaultDbConnection($conn);

        $response = $filter_chain->next($request, $callback);

        $this->_printStat($response, $conn->getStats());

        return $response;
    }

    protected function _printStat($response, $info_arr)
    {
        $time = (new lmbDateTime())->format("Y-m-d h:i:s");
        $output = $time . "\n";
        $total_time = 0;
        $i = 1;
        foreach ($info_arr as $info) {
            $result = [];
            $result[] = 'num: ' . $i++;
            $result[] = 'query: ' . $info['query'];
            if (isset($info['params']) && is_array($info['params']))
                $result[] = 'params: ' . json_encode($info['params']);
            $result[] = 'time: ' . $info['time'];
            $output .= implode("\n", $result) . "\n";

            $total_time += $info['time'];
        }
        $output .= "Total Time: " . $total_time . "\n";

        if ($response->getContentType() == 'text/html') {
            $response->append("<!--" . $output . " -->");
        }

        $log = new lmbLog();
        $log->registerWriter(new lmbLogPlainFileWriter(lmbEnv::get('LIMB_VAR_DIR') . "/log/db.log"));
        $log->log(LOG_INFO, $output);

    }
}
