<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\validation\cases\rule;

use PHPUnit\Framework\TestCase;
use limb\validation\src\lmbErrorList;

abstract class lmbValidationRuleTestCase extends TestCase
{
    protected $error_list;

    function setUp(): void
    {
        $this->error_list = $this->createMock(lmbErrorList::class);
    }
}
