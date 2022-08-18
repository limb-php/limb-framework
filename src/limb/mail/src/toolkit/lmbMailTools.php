<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2010 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\mail\src\toolkit;

use limb\toolkit\src\lmbAbstractTools;
use limb\mail\src\lmbMailer;
use limb\mail\src\lmbMemoryMailer;
use limb\mail\src\lmbResponseMailer;
use limb\mail\src\lmbMailService;
use limb\mail\src\lmbMacroTemplateMail;

/**
 * class lmbMailTools
 *
 * @package mail
 */
class lmbMailTools extends lmbAbstractTools
{
  function getMailTemplate($template_id, $template_parser = null)
  {
  	$conf = $this->toolkit->getConf('mail');  	
  	
  	if (!$template_parser)
  	  if (isset($conf['macro_template_parser']))
  	    $template_parser = $conf['macro_template_parser'];

  	switch ($template_parser)
  	{
  		default:
  		case 'only_body':
  		case 'mailpart': 
  		  $template_parser_class = 'lmbMacroTemplateMail'; break;
  		case 'newline':
  		  $template_parser_class = 'lmbMailService';
  		break;
  	}
  	
  	$mail_template = new $template_parser_class($template_id);
  	$mail_template->setDefaultSender($conf['sender']);
  	return $mail_template;
  }
	
  function getMailer()
  {
  	$conf = $this->toolkit->getConf('mail');  	
  	$mailer_class = 'lmbMailer';
  	
  	if (isset($conf['mode']))
    {
      if ($conf['mode'] == 'testing')
        $mailer_class = 'lmbMemoryMailer';
      elseif ($conf['mode'] == 'devel')
        $mailer_class = 'lmbResponseMailer';
    }
    
    $mailer = new $mailer_class;
    $mailer->setConfig($conf);
    return $mailer;
  }
}