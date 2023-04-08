<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\dbal\cases\nondriver\query;

require_once('.setup.php');

use PHPUnit\Framework\TestCase;
use limb\dbal\src\query\lmbSelectQuery;

class lmbSelectQueryTest extends TestCase
{
  function testConstruct()
  {
    $sql = new lmbSelectQuery('foo');

    $this->assertEquals($sql->getTables(), array('foo'));
  }

}
