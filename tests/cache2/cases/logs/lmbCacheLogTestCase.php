<?php

namespace tests\cache2\cases\logs;

use PHPUnit\Framework\TestCase;
use limb\cache2\src\lmbCacheInterface;

require_once dirname(__FILE__) . '/../.setup.php';

abstract class lmbCacheLogTestCase extends TestCase
{
    /**
     * @var \limb\cache2\src\logs\lmbCacheLogInterface
     */
    protected $logger;

    /**
     * @todo
     */
    function testAddRecord_GetRecords()
    {
        $this->logger->addRecord($key1 = 'foo1', $operation1 = lmbCacheInterface::OPERATION_ADD, $time1 = 42, $result1 = true);
        $this->logger->addRecord($key2 = 'foo2', $operation2 = lmbCacheInterface::OPERATION_DECREMENT, $time2 = 43, $result2 = false);

        $records = $this->logger->getRecords();

        $this->assertEquals(2, count($records));

        $this->assertEquals($records[0], array(
            'key' => $key1,
            'operation' => $operation1,
            'time' => $time1,
            'result' => $result1
        ));

        $this->assertEquals($records[1], array(
            'key' => $key2,
            'operation' => $operation2,
            'time' => $time2,
            'result' => $result2
        ));
    }

}
