<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\web_app\cases;

require_once '.setup.php';

use PHPUnit\Framework\TestCase;
use limb\dbal\src\lmbSimpleDb;
use limb\toolkit\src\lmbToolkit;

class lmbWebAppTestCase extends TestCase
{
    protected $toolkit;
    protected $request;
    protected $response;
    protected $db;
    protected $connection;

    function setUp(): void
    {
        $this->toolkit = lmbToolkit::save();
        $this->request = $this->toolkit->getRequest();
        $this->response = $this->toolkit->getResponse();
        $this->session = $this->toolkit->getSession();
        $this->session->reset();
        $this->connection = $this->toolkit->getDefaultDbConnection();
        $this->db = new lmbSimpleDb($this->connection);
    }

    function tearDown(): void
    {
        lmbToolkit::restore();
    }

    function assertCommandValid($command, $line)
    {
        $this->assertTrue($command->isValid());

        // else

        $errors = array();
        foreach ($command->getErrorList() as $error)
            $errors[] .= ' ' . $error->get();
        $error_text = implode(', ', $errors);
        $this->fail('Command is not valid with following errors: ' . $error_text . ' at line ' . $line);
    }
}
