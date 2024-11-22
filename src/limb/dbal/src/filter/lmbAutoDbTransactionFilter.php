<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\filter;

use limb\dbal\src\drivers\lmbAutoTransactionConnection;
use limb\toolkit\src\lmbToolkit;

/**
 * class lmbAutoDbTransactionFilter.
 *
 * @package dbal
 * @version $Id: lmbAutoDbTransactionFilter.php 7486 2009-01-26 19:13:20Z
 */
class lmbAutoDbTransactionFilter
{
    function run($filter_chain, $request = null, $callback = null)
    {
        $toolkit = lmbToolkit::instance();
        $old_conn = $toolkit->getDefaultDbConnection();
        $conn = new lmbAutoTransactionConnection($old_conn);
        $toolkit->setDefaultDbConnection($conn);

        try {
            $response = $filter_chain->next($request, $callback);

            $conn->commitTransaction();
            $toolkit->setDefaultDbConnection($old_conn);
        } catch (\Exception $e) {
            $conn->rollbackTransaction();
            $toolkit->setDefaultDbConnection($old_conn);
            throw $e;
        }

        return $response;
    }
}
