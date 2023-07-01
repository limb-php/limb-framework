<?php
namespace tests\mail\cases;

use limb\mail\src\lmbMacroTemplateMail;
use tests\macro\cases\lmbBaseMacroTestCase;
use limb\fs\src\lmbFs;
use limb\toolkit\src\lmbToolkit;

require_once '.setup.php';

class lmbMacroTemplateMailTest extends lmbBaseMacroTestCase
{
  function setUp(): void
  {
    parent::setUp();

    lmbFs::mkdir($this->tpl_dir . '/_mail');
    $toolkit = lmbToolkit::instance();
    $toolkit->setConf('macro', $this->_createMacroConfig());
    $mail_config = $toolkit->getConf('mail');
    $mail_config->set('mode', 'testing');
    $toolkit->setConf('mail', $mail_config);
  }
  
  function tearDown(): void
  {

  }

  function testSimpleMailTemplate()
  {
  	$mail_template = '{$#text}';

    $this->_createTemplate($mail_template, '_mail/testMailTemplate.phtml');
    $mail = new lmbMacroTemplateMail('testMailTemplate');
    $mail->set('text', 'test_text');
    $mailer = $mail->sendTo('test@mail.com', 'test subject');
    
    $this->assertEquals('test_text', $mailer->html);
  }
  
  function testMailTemplateWithMailpartTags()
  {
  	$mail_template = '
  	{{mailpart name="subject"}}
  	{$#subject}
  	{{/mailpart}}
  	
  	{{mailpart name="html_body"}}
  	<h1>{$#html}</h1>
  	{{/mailpart}}
  	
  	{{mailpart name="txt_body"}}
  	TXT
  	{{/mailpart}}';

    $this->_createTemplate($mail_template, '_mail/testMailpart.phtml');
    $mail = new lmbMacroTemplateMail('testMailpart');
    $mail->set('subject', 'test_subject');
    $mail->set('html', 'test_html');
    $mailer = $mail->sendTo('test@mail.com');
    
    $this->assertEquals('test_subject', $mailer->subject);
    $this->assertEquals('<h1>test_html</h1>', $mailer->html);
    $this->assertEquals('TXT', $mailer->text);
  }
}
