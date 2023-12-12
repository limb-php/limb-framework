<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\web_app\cases\db\validation\rule;

use Tests\validation\cases\rule\lmbValidationRuleTestCase;
use limb\dbal\src\lmbSimpleDb;
use limb\web_app\src\validation\rule\UniqueTableFieldRule;
use limb\toolkit\src\lmbToolkit;
use limb\core\src\lmbSet;

class lmbUniqueTableFieldRuleTest extends lmbValidationRuleTestCase
{
    var $db = null;

    protected $error_list;

    function setUp(): void
    {
        parent::setUp();

        $toolkit = lmbToolkit::instance();
        $conn = $toolkit->getDefaultDbConnection();
        $this->db = new lmbSimpleDb($conn);
        $this->db->insert('test_table', array('field1' => 1, 'field2' => 'wow'), null);
        $this->db->insert('test_table', array('field1' => 2, 'field2' => 'blah'), null);
    }

    function tearDown(): void
    {
        parent::tearDown();
    }

    function testFieldValid()
    {
        $rule = new UniqueTableFieldRule('test', 'test_table', 'field1');

        $data = new lmbSet();
        $data->set('test', -10000);

        $this->error_list
            ->expects($this->never())
            ->method('addError');

        $rule->validate($data, $this->error_list);
    }

    function testFieldNotValid()
    {
        $rule = new UniqueTableFieldRule('test', 'test_table', 'field2');

        $data = new lmbSet();
        $data->set('test', 'wow');

        $this->error_list
            ->expects($this->once())
            ->method('addError')
            ->with(lmb_i18n('{Field} must have other value since {Value} already exists', 'web_app'),
                array('Field' => 'test'),
                array('Value' => 'wow'));


        $rule->validate($data, $this->error_list);

    }

    function testFieldNotValid2()
    {
        $rule = new UniqueTableFieldRule('test', 'test_table', 'field1');

        $data = new lmbSet();
        $data->set('test', "001");

        $this->error_list
            ->expects($this->once())
            ->method('addError')
            ->with(lmb_i18n('{Field} must have other value since {Value} already exists', 'web_app'),
                array('Field' => 'test'),
                array('Value' => '001'));


        $rule->validate($data, $this->error_list);
    }

    function testFieldNotValidSelfError()
    {
        $rule = new UniqueTableFieldRule('test', 'test_table', 'field2', $message = "ERROR_DUPLICATE_WOW");

        $data = new lmbSet();
        $data->set('test', 'wow');

        $this->error_list
            ->expects($this->once())
            ->method('addError')
            ->with($message, array('Field' => 'test'), array('Value' => 'wow'));

        $rule->validate($data, $this->error_list);
    }
}
