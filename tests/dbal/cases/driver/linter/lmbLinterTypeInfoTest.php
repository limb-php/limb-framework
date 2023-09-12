<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace Tests\dbal\cases\driver\linter;

use limb\dbal\src\drivers\linter\lmbLinterRecord;
use limb\dbal\src\drivers\linter\lmbLinterStatement;
use limb\toolkit\src\lmbToolkit;
use tests\dbal\cases\driver\DriverTypeInfoTestBase;

class lmbLinterTypeInfoTest extends DriverTypeInfoTestBase
{

  function setUp(): void
  {
      parent::init(lmbLinterStatement::class,
          lmbLinterRecord::class);

    $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
    $this->typeInfo = $this->connection->getTypeInfo();

    parent::setUp();
  }
}


