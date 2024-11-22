<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\i18n\src\scanner;

/**
 * class lmbTSDocument.
 *
 * Dom representation of ts-files (qt translation)
 *
 * @package i18n
 * @version $Id: lmbTSDocument.php 7994 2009-09-21 13:01:14Z
 */
class lmbTSDocument extends \DOMDocument
{

    function addMessage($message)
    {
        $message_node = $this->createElement('message');
        $source_node = $this->createElement('source', $message);
        $message_node->appendChild($source_node);

        $target = $this->getElementsByTagName('context')->item(0);
        $target->appendChild($message_node);
    }
}
