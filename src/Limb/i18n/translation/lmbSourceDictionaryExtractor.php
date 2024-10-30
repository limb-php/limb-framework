<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\i18n\translation;

/**
 * abstract class lmbSourceDictionaryExtractor.
 *
 * @package i18n
 * @version $Id: lmbSourceDictionaryExtractor.php 7486 2009-01-26 19:13:20Z
 */
abstract class lmbSourceDictionaryExtractor
{
    abstract function extract($code, &$dictionaries = array(), $response = null);

    function extractFromFile($file, &$dictionaries = array(), $response = null)
    {
        $this->extract(file_get_contents($file), $dictionaries, $response);
    }
}
