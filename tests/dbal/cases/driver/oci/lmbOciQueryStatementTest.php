<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\dbal\cases\driver\oci;

use limb\dbal\src\drivers\oci\lmbOciQueryStatement;
use limb\toolkit\src\lmbToolkit;
use PHPUnit\Framework\TestCase;

require_once(dirname(__FILE__) . '/.setup.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbOciQueryStatementTest extends TestCase
{
    function setUp(): void
    {
        if( !function_exists('oci_execute') )
            $this->markTestSkipped('no driver oci');

        $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
        DriverOciSetup($this->connection->getConnectionId());

        parent::setUp();
    }

    function testPaginate()
    {
        $stmt = new lmbOciQueryStatement($this->connection, 'SELECT * FROM founding_fathers');
        $stmt->paginate(1, 1);
        $rs = $stmt->getRecordSet();

        $rs->rewind();
        $record = $rs->current();
        $this->assertEquals($record->get('first'), 'Alexander');

        $rs->next();
        $this->assertFalse($rs->valid());

        $this->assertEquals($rs->count(), 1);
    }

    function testPaginateBindedStatement()
    {
        $stmt = new lmbOciQueryStatement($this->connection, 'SELECT * FROM founding_fathers WHERE first=:first:');
        $stmt->set('first', 'Alexander');
        $stmt->paginate(0, 1);
        $rs = $stmt->getRecordSet();

        $rs->rewind();
        $record = $rs->current();
        $this->assertEquals($record->get('first'), 'Alexander');

        $rs->next();
        $this->assertFalse($rs->valid());

        $this->assertEquals($rs->count(), 1);
    }
}
