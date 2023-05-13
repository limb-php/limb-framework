<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\dbal\cases\driver\oci;

use limb\dbal\src\drivers\oci\lmbOciRecord;
use limb\dbal\src\drivers\oci\lmbOciStatement;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverTypeInfoTestBase;

class lmbOciTypeInfoTest extends DriverTypeInfoTestBase
{
  function setUp(): void
  {
      parent::init(lmbOciStatement::class, lmbOciRecord::class);

    $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
    $this->typeInfo = $this->connection->getTypeInfo();

    parent::setUp();
  }
}
