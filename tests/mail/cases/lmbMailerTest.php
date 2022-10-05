<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

use PHPUnit\Framework\TestCase;
use limb\mail\src\lmbMailer;

class lmbMailerTest extends TestCase
{

  function testConstructorConfiguration()
  {
    $config = array('smtp_port' => '252525');

    $mailer = new lmbMailer($config);
    $this->assertEquals($mailer->smtp_host, 'localhost');
    $this->assertEquals($mailer->smtp_port, '252525');
  }

  function testSetDefaultConfig()
  {
    $mailer = new lmbMailer();
    $this->assertEquals($mailer->smtp_host, 'localhost');
  }

  function testSetConfig()
  {
    $mailer = new lmbMailer(array());

    $mailer->smtp_host = 'foo';

    $config = array('smtp_port' => 'baz');
    $mailer->setConfig($config);

    $this->assertEquals($mailer->smtp_host, 'foo');
    $this->assertEquals($mailer->smtp_port, 'baz');
  }

  function testProcessMailRecepients()
  {
    $mailer = new lmbMailer(array());
    
    $recs = $mailer->processMailRecipients("bob@localhost");
    $this->assertEquals(sizeof($recs), 1);
    $this->assertEquals($recs[0]['address'], "bob@localhost");
    $this->assertEquals($recs[0]['name'], "");

    $recs = $mailer->processMailRecipients("Bob<bob@localhost>");
    $this->assertEquals(sizeof($recs), 1);
    $this->assertEquals($recs[0]['address'], "bob@localhost");
    $this->assertEquals($recs[0]['name'], "Bob");

    $recs = $mailer->processMailRecipients(array("bob@localhost"));
    $this->assertEquals(sizeof($recs), 1);
    $this->assertEquals($recs[0]['address'], "bob@localhost");
    $this->assertEquals($recs[0]['name'], "");

    $recs = $mailer->processMailRecipients(array("name" => "Bob", "address" => "bob@localhost"));
    $this->assertEquals(sizeof($recs), 1);
    $this->assertEquals($recs[0]['address'], "bob@localhost");
    $this->assertEquals($recs[0]['name'], "Bob");

    $recs = $mailer->processMailRecipients(array("Bob<bob@localhost>"));
    $this->assertEquals(sizeof($recs), 1);
    $this->assertEquals($recs[0]['address'], "bob@localhost");
    $this->assertEquals($recs[0]['name'], "Bob");

    $recs = $mailer->processMailRecipients(array("bob@localhost", "todd@localhost"));
    $this->assertEquals(sizeof($recs), 2);
    $this->assertEquals($recs[0]['address'], "bob@localhost");
    $this->assertEquals($recs[0]['name'], "");
    $this->assertEquals($recs[1]['address'], "todd@localhost");
    $this->assertEquals($recs[1]['name'], "");

    $recs = $mailer->processMailRecipients(array("Bob<bob@localhost>", "todd@localhost"));
    $this->assertEquals(sizeof($recs), 2);
    $this->assertEquals($recs[0]['address'], "bob@localhost");
    $this->assertEquals($recs[0]['name'], "Bob");
    $this->assertEquals($recs[1]['address'], "todd@localhost");
    $this->assertEquals($recs[1]['name'], "");

    $recs = $mailer->processMailRecipients(array(array("name" => "Bob", "address" => "bob@localhost"), "todd@localhost"));
    $this->assertEquals(sizeof($recs), 2);
    $this->assertEquals($recs[0]['address'], "bob@localhost");
    $this->assertEquals($recs[0]['name'], "Bob");
    $this->assertEquals($recs[1]['address'], "todd@localhost");
    $this->assertEquals($recs[1]['name'], "");
  }

  function testBugWithUndefinedPhpMailVersionVariable()
  {
    $mailer = new lmbMailer(array('use_phpmail' => true));
  }

}
