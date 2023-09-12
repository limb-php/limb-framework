<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace Tests\mail\cases;

use PHPUnit\Framework\TestCase;
use limb\mail\src\lmbMailer;

require '.setup.php';

class lmbMailerTest extends TestCase
{

  function testConstructorConfiguration()
  {
    $config = array('smtp_port' => '252525');

    $mailer = new lmbMailer($config);
    $this->assertEquals('localhost', $mailer->smtp_host);
    $this->assertEquals('252525', $mailer->smtp_port);
  }

  function testSetDefaultConfig()
  {
    $mailer = new lmbMailer();
    $this->assertEquals('localhost', $mailer->smtp_host);
  }

  function testSetConfig()
  {
    $mailer = new lmbMailer(array());

    $mailer->smtp_host = 'foo';

    $config = array('smtp_port' => 'baz');
    $mailer->setConfig($config);

    $this->assertEquals('foo', $mailer->smtp_host);
    $this->assertEquals('baz', $mailer->smtp_port);
  }

  function testProcessMailRecepients()
  {
    $mailer = new lmbMailer(array());
    
    $recs = $mailer->processMailRecipients("bob@localhost");
    $this->assertEquals(1, sizeof($recs));
    $this->assertEquals("bob@localhost", $recs[0]['address']);
    $this->assertEquals("", $recs[0]['name']);

    $recs = $mailer->processMailRecipients("Bob<bob@localhost>");
    $this->assertEquals(1, sizeof($recs));
    $this->assertEquals("bob@localhost", $recs[0]['address']);
    $this->assertEquals("Bob", $recs[0]['name']);

    $recs = $mailer->processMailRecipients(array("bob@localhost"));
    $this->assertEquals(1, sizeof($recs));
    $this->assertEquals("bob@localhost", $recs[0]['address']);
    $this->assertEquals("", $recs[0]['name']);

    $recs = $mailer->processMailRecipients(array("name" => "Bob", "address" => "bob@localhost"));
    $this->assertEquals(1, sizeof($recs));
    $this->assertEquals("bob@localhost", $recs[0]['address']);
    $this->assertEquals("Bob", $recs[0]['name']);

    $recs = $mailer->processMailRecipients(array("Bob<bob@localhost>"));
    $this->assertEquals(1, sizeof($recs));
    $this->assertEquals("bob@localhost", $recs[0]['address']);
    $this->assertEquals("Bob", $recs[0]['name']);

    $recs = $mailer->processMailRecipients(array("bob@localhost", "todd@localhost"));
    $this->assertEquals(2, sizeof($recs));
    $this->assertEquals("bob@localhost", $recs[0]['address']);
    $this->assertEquals("", $recs[0]['name']);
    $this->assertEquals("todd@localhost", $recs[1]['address']);
    $this->assertEquals("", $recs[1]['name']);

    $recs = $mailer->processMailRecipients(array("Bob<bob@localhost>", "todd@localhost"));
    $this->assertEquals(2, sizeof($recs));
    $this->assertEquals("bob@localhost", $recs[0]['address']);
    $this->assertEquals("Bob", $recs[0]['name']);
    $this->assertEquals("todd@localhost", $recs[1]['address']);
    $this->assertEquals("", $recs[1]['name']);

    $recs = $mailer->processMailRecipients(array(array("name" => "Bob", "address" => "bob@localhost"), "todd@localhost"));
    $this->assertEquals(2, sizeof($recs));
    $this->assertEquals("bob@localhost", $recs[0]['address']);
    $this->assertEquals("Bob", $recs[0]['name']);
    $this->assertEquals("todd@localhost", $recs[1]['address']);
    $this->assertEquals("", $recs[1]['name']);
  }

  function testBugWithUndefinedPhpMailVersionVariable()
  {
    $mailer = new lmbMailer(array('use_phpmail' => true));

    $this->assertTrue(true);
  }

}
