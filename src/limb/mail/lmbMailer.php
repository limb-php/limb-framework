<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\mail;

use limb\toolkit\lmbToolkit;
use limb\core\exception\lmbException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * class lmbMailer.
 *
 * @package mail
 * @version $Id: lmbMailer.php 8145 2010-03-05 19:53:29Z
 */
class lmbMailer implements lmbBaseMailerInterface
{
    protected $attachments = array();
    protected $images = array();
    protected $replyTo = array();

    public $use_phpmail;
    public $smtp_host;
    public $smtp_port;
    public $smtp_auth;
    public $smtp_user;
    public $smtp_password;
    public $smtp_secure;
    public $smtp_debug;

    function __construct($config = array())
    {
        $conf = lmbToolkit::instance()->getConf('mail');

        $this->use_phpmail = $conf['use_phpmail'];
        $this->smtp_host = $conf['smtp_host'];
        $this->smtp_port = $conf['smtp_port'];
        $this->smtp_auth = $conf['smtp_auth'];
        $this->smtp_user = $conf['smtp_user'];
        $this->smtp_password = $conf['smtp_password'];
        $this->smtp_secure = $conf['smtp_secure'] ?? '';
        $this->smtp_debug = $conf['smtp_debug'] ?? false;

        $this->setConfig($config);
    }

    public function setConfig($config)
    {
        foreach ($config as $property_name => $property_value)
            $this->$property_name = $property_value;
    }

    protected function _createMailer()
    {
        $mailer = new PHPMailer(true);

        if ($this->use_phpmail)
            return $mailer;

        $mailer->isSMTP();
        $mailer->Host = $this->smtp_host;
        $mailer->Port = $this->smtp_port;
        $mailer->SMTPSecure = $this->smtp_secure ? PHPMailer::ENCRYPTION_SMTPS : false;
        $mailer->SMTPDebug = $this->smtp_debug ? SMTP::DEBUG_SERVER : false;

        if ($this->smtp_auth) {
            $mailer->SMTPAuth = true;
            $mailer->Username = $this->smtp_user;
            $mailer->Password = $this->smtp_password;
        }
        return $mailer;
    }

    function addAttachment($path, $name = "", $encoding = "base64", $type = "application/octet-stream")
    {
        $this->attachments[] = array(
            'path' => $path,
            'name' => $name,
            'encoding' => $encoding,
            'type' => $type
        );
    }

    function embedImage($path, $cid, $name = "", $encoding = "base64", $type = "application/octet-stream")
    {
        $this->images[] = array(
            'path' => $path,
            'cid' => $cid,
            'name' => $name,
            'encoding' => $encoding,
            'type' => $type
        );
    }

    function sendPlainMail($recipients, $sender, $subject, $body, $charset = 'utf-8')
    {
        try {
            $mailer = $this->_createMailer();

            $mailer->IsHTML(false);
            $mailer->CharSet = $charset;

            if (!empty($this->attachments))
                $this->_addAttachments($mailer);

            if (!empty($this->images))
                $this->_addEmbeddedImages($mailer);

            $this->_addRepliesTo($mailer);

            $recipients = $this->processMailRecipients($recipients);

            foreach ($recipients as $recipient)
                $mailer->AddAddress($recipient['address'], $recipient['name']);

            if (!$sender = $this->processMailAddressee($sender))
                return false;

            $mailer->From = $sender['address'];
            $mailer->FromName = $sender['name'];
            $mailer->Sender = $sender['address'];
            $mailer->Subject = $subject;
            $mailer->Body = $body;

            return $mailer->Send();
        } catch (Exception $e) {
            throw new lmbException($e->getMessage());
        }
    }

    function addReplyTo($replyTo)
    {
        $this->replyTo[] = $replyTo;
    }

    function sendHtmlMail($recipients, $sender, $subject, $html, $text = null, $charset = 'utf-8')
    {
        try {
            $mailer = $this->_createMailer();

            $mailer->IsHTML(true);
            $mailer->CharSet = $charset;

            $mailer->Body = $html;

            if (!empty($this->attachments))
                $this->_addAttachments($mailer);

            if (!empty($this->images))
                $this->_addEmbeddedImages($mailer);

            $this->_addRepliesTo($mailer);

            if (!is_null($text))
                $mailer->AltBody = $text;

            $recipients = $this->processMailRecipients($recipients);

            foreach ($recipients as $recipient)
                $mailer->AddAddress($recipient['address'], $recipient['name']);

            $sender = $this->processMailAddressee($sender);
            if (!$sender)
                return false;

            $mailer->From = $sender['address'];
            $mailer->FromName = $sender['name'];
            $mailer->Sender = $sender['address'];
            $mailer->Subject = $subject;

            return $mailer->Send();
        } catch (Exception $e) {
            throw new lmbException($e->getMessage());
        }
    }

    function processMailRecipients($recipients)
    {
        if (!is_array($recipients) || isset($recipients['name']))
            $recipients = array($recipients);

        $result = array();
        foreach ($recipients as $recipient) {
            if ($recipient = $this->processMailAddressee($recipient))
                $result[] = $recipient;
        }

        return $result;
    }

    function processMailAddressee($adressee)
    {
        if (is_array($adressee)) {
            if (isset($adressee['address']) && array_key_exists('name', $adressee))
                return $adressee;

            return null;
        } elseif (preg_match('~("|\')?([^"\']+)("|\')?\s*<([^>]+)>~u', $adressee, $matches))
            return array('address' => $matches[4], 'name' => $matches[2]);
        else
            return array('address' => $adressee, 'name' => '');
    }

    protected function _addAttachments($mailer)
    {
        foreach ($this->attachments as $attachment)
            $mailer->AddAttachment($attachment['path'],
                $attachment['name'],
                $attachment['encoding'],
                $attachment['type']);
    }

    protected function _addEmbeddedImages($mailer)
    {
        foreach ($this->images as $image) {
            $mailer->AddEmbeddedImage($image['path'],
                $image['cid'],
                $image['name'],
                $image['encoding'],
                $image['type']);
        }
    }

    protected function _addRepliesTo($mailer)
    {
        if (!$this->replyTo)
            return;

        $recipients = $this->processMailRecipients($this->replyTo);

        foreach ($recipients as $recipient)
            $mailer->AddReplyTo($recipient['address'], $recipient['name']);
    }
}
