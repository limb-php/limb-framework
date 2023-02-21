<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\dbal\cases\nondriver;

require_once(dirname(__FILE__) . '/.setup.php');

use PHPUnit\Framework\TestCase;
use limb\dbal\src\criteria\lmbSQLFieldCriteria;
use limb\dbal\src\lmbTableGateway;
use limb\dbal\src\drivers\lmbDbCachedInfo;
use limb\toolkit\src\lmbToolkit;

class lmbTableGatewayMetadataTest extends TestCase
{
  var $conn = null;

  function setUp(): void
  {
    $toolkit = lmbToolkit::save();
    $this->conn = $toolkit->getDefaultDbConnection();
  }

  function tearDown(): void
  {
    lmbToolkit::restore();
  }

  function testFillMetaInfoFromDB()
  {
    $table = new lmbTableGateway('all_types_test', $this->conn);

    $expected = array('field_int' => 'field_int',
                      'field_varchar' => 'field_varchar',
                      'field_char' => 'field_char',
                      'field_date' => 'field_date',
                      'field_datetime' => 'field_datetime',
                      'field_time' => 'field_time',
                      'field_text' => 'field_text',
                      'field_smallint' => 'field_smallint',
                      'field_bigint' => 'field_bigint',
                      'field_blob' => 'field_blob',
                      'field_float' => 'field_float',
                      'field_decimal' => 'field_decimal',
                      'field_tinyint' => 'field_tinyint');

    $this->assertEquals($table->getColumnNames(), $expected);
  }
}
