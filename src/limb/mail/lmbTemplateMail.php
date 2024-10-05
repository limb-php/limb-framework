<?php

namespace limb\mail;

use limb\core\lmbSet;
use limb\toolkit\lmbToolkit;
use limb\core\exception\lmbException;

class lmbTemplateMail
{
    protected $template_id;
    protected $dataset;
    protected $default_sender;

    function __construct($template_id)
    {
        $this->template_id = $template_id;
        $this->dataset = new lmbSet;
    }

    function set($name, $value)
    {
        $this->dataset->set($name, $value);
    }

    function sendMailTo($recipients, $subject = null, $sender = null, $charset = 'utf-8')
    {
        return $this->sendTo($recipients, $subject, $sender, $charset);
    }

    function sendTo($recipients, $subject = null, $sender = null, $charset = 'utf-8')
    {
        $tools = lmbToolkit::instance();
        $parts = self::_parseMailpartTags($this->_renderTemplate($tools));

        if (!$sender) {
            if (isset($parts['sender']))
                $sender = $parts['sender'];
            else
                $sender = $this->getDefaultSender();
        }

        if (!$subject) {
            if (!isset($parts['subject']))
                throw new lmbException('Subject required for mail message');
            else $subject = $parts['subject'];
        }

        $html = $parts['html_body'] ?? null;
        $text = $parts['txt_body'] ?? null;

        $mailer = $tools->getMailer();

        if ($text and !$html) {
            $mailer->sendPlainMail($recipients, $sender, $subject, $text, $charset);
        } elseif ($html) {
            $mailer->sendHtmlMail($recipients, $sender, $subject, $html, $text, $charset);
        } else
            throw new lmbException('Contents required for mail message');

        return $mailer;
    }

    function setDefaultSender($sender)
    {
        $this->default_sender = $sender;
    }

    function getDefaultSender()
    {
        return $this->default_sender;
    }

    protected function _renderTemplate($tools)
    {
        $template_file = $this->template_id . '.html';
        $path = $tools->locateTemplateByAlias('_mail/' . $template_file);

        return file_get_contents($path);
    }

    protected static function _parseMailpartTags($content)
    {
        if (!stripos($content, '</mailpart>'))
            return array('html_body' => $content);

        try {
            $doc = simplexml_load_string('<?xml version="1.0"?><mail>' . $content . '</mail>');
        } catch (\Exception $e) {
            throw new lmbException('Error while parsing template: ' . $e->getMessage());
        }

        if (count($doc->mailpart) < 2)
            throw new \Exception('{{mailpart name="subject"}}, {{mailpart name="html_body"}} or {{mailpart name="txt_body"}} tags must be defined in template');

        $fields = array();
        foreach ($doc->mailpart as $mailpart)
            $fields[(string)$mailpart['name']] = trim($mailpart);

        return $fields;
    }
}
