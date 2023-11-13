<?php
namespace Tests\mail\cases;

use Tests\macro\cases\lmbBaseMacroTestCase;
use limb\mail\src\lmbMailService;
use limb\fs\src\lmbFs;
use limb\toolkit\src\lmbToolkit;

require '.setup.php';

class lmbMailServiceTest extends lmbBaseMacroTestCase
{
  function setUp(): void
  {
    parent::setUp();

    lmbFs::mkdir($this->tpl_dir . '/_mail');
    lmbToolkit::instance()->setConf('macro', $this->_createMacroConfig());
  }

  function tearDown(): void
  {

  }

  function testGetMailHtmlContent()
  {
  	$mail_template = <<<EOD
subj

{\$#foo}bar
EOD;

    $this->_createTemplate($mail_template, '_mail/testGetMailHtmlContent.phtml');
    $service = new lmbMailService('testGetMailHtmlContent');
    $service->set('foo', 42);

    $this->assertEquals('subj', $service->getSubject());
    $this->assertEquals('42bar', $service->getHtmlContent());
  }

  function testGetMailTextContent()
  {
    $mail_template = <<<EOD
subj

{\$#bar}foo
EOD;

    $this->_createTemplate($mail_template, '_mail/testGetMailTextContent.phtml');

    $service = new lmbMailService('testGetMailTextContent');
    $service->set('bar', 11);

    $this->assertEquals('subj', $service->getSubject());
    $this->assertEquals('11foo', $service->getTextContent());
  }

  function testGetMailBothContents()
  {
  	$mail_template = <<<EOD
{\$#subj_dynamic}subj

{\$#text_dynamic}text_static

{\$#html_dynamic}html_static
EOD;

    $this->_createTemplate($mail_template, '_mail/testGetMailBothContents.phtml');

    $service = new lmbMailService('testGetMailBothContents');
    $service->set('subj_dynamic', 4);
    $service->set('text_dynamic', 8);
    $service->set('html_dynamic', 15);

    $this->assertEquals('4subj', $service->getSubject());
    $this->assertEquals('8text_static', $service->getTextContent());
    $this->assertEquals('15html_static', $service->getHtmlContent());
  }
}
