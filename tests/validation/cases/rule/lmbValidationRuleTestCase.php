<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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
