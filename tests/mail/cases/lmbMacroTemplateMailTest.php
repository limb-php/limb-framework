<?php
lmb_require('limb/mail/src/lmbMacroTemplateMail.class.php');
lmb_require('limb/macro/tests/cases/lmbBaseMacroTest.class.php');

class lmbMacroTemplateMailTest extends lmbBaseMacroTest
{
  function setUp()
  {
    parent::setUp();
    lmbFs::mkdir($this->tpl_dir . '/_mail');
    $toolkit = lmbToolkit::instance();
    $toolkit->setConf('macro', $this->_createMacroConfig());
    $mail_config = $toolkit->getConf('mail');
    $mail_config->set('mode', 'testing');
    $toolkit->setConf('mail', $mail_config);
  }
  
  function tearDown(){}

  function testSimpleMailTemplate()
  {
  	$mail_template = '{$#text}';

    $this->_createTemplate($mail_template, '_mail/testMailTemplate.phtml');
    $mail = new lmbMacroTemplateMail('testMailTemplate');
    $mail->set('text', 'test_text');
    $mailer = $mail->sendTo('test@mail.com', 'test subject');
    
    $this->assertEquals($mailer->html, 'test_text');
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
    
    $this->assertEquals($mailer->subject, 'test_subject');
    $this->assertEquals($mailer->html, '<h1>test_html</h1>');
    $this->assertEquals($mailer->text, 'TXT');
  }
}