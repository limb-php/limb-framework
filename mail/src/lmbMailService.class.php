<?php
namespace limb\mail\src;

use limb\core\src\lmbSet;
use limb\mail\src\lmbMailer;
use limb\mail\src\lmbMemoryMailer;
use limb\mail\src\lmbResponseMailer;
use limb\macro\src\lmbMacroTemplate;

class lmbMailService
{
  protected $template_id;
  /**
   * @var Member
   */
  protected $member;

  protected $dto;
  /**
   * @var lmbBaseMailerInterface
   */
  protected $mailer;
  protected $separator;

  protected $subject;
  protected $html_content;
  protected $text_content;
  
  protected $default_sender;

  function __construct($template_id)
  {
    $this->template_id = $template_id;
    $this->dto = new lmbSet;
    $this->separator = "\n\n";
  }

  function set($name, $value)
  {
    $this->dto->set($name, $value);
  }

  protected function _parseMailTemplate($postfix = '')
  {
  	$tools = lmbToolkit::instance();

    $template_file = $this->template_id . $postfix . '.phtml';
  	$path = $tools->locateTemplateByAlias('_mail/'.$template_file);

    $template = new lmbMacroTemplate($path, $tools->getConf('macro'));
    $template->setVars($this->dto->export());
    $raw_content = $template->render();

    $parts = explode($this->separator, $raw_content);

    if(1 === count($parts))
      throw new lmbException('Subject must be on the top of mail template separated by "'.$this->separator.'"');

    $this->subject = $parts[0];
    $this->text_content = $parts[1];

    if(3 === count($parts))
      $this->html_content = $parts[2];
    else
      $this->html_content = $this->text_content;
  }

  function getSubject()
  {
    if(is_null($this->subject))
     $this->_parseMailTemplate();

    return $this->subject;
  }

  function getHtmlContent()
  {
  	if(is_null($this->subject))
  	 $this->_parseMailTemplate();

    return $this->html_content;
  }

  function getTextContent()
  {
  	if(is_null($this->subject))
     $this->_parseMailTemplate();

    return $this->text_content;
  }

  function sendMailTo($email)
  {
    $this->mailer = lmbToolkit::instance()->getMailer();
    $this->mailer->sendHtmlMail($email,
                          $this->getDefaultSender(),
                          $this->getSubject(),
                          $this->getHtmlContent(),
                          $this->getTextContent());
  }

  function getMailer()
  {
    return $this->mailer;
  }
  
  function setDefaultSender($sender)
  {
  	$this->default_sender = $sender; 
  }
  
  function getDefaultSender()
  {
  	return $this->default_sender;
  }
}
