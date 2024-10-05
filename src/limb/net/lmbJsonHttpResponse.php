<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\net;

use limb\core\exception\lmbException;

/**
 * class lmbJsonHttpResponse.
 *
 * @package net
 * @version $Id$
 */
class lmbJsonHttpResponse extends lmbHttpResponse
{
    protected $use_emulation = false;

    // Encode <, >, ', &, and " characters in the JSON, making it also safe to be embedded into HTML.
    // 15 === JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
    public const DEFAULT_ENCODING_OPTIONS = 15;

    protected $encodingOptions = self::DEFAULT_ENCODING_OPTIONS;


    public function __construct($content = [], $status = 200, $headers = [])
    {
        $content = $this->_toJson($content);

        parent::__construct($content, $status, $headers);

        $this->addHeader('Content-type', 'application/json');
    }

    function useEmulation($value)
    {
        $this->use_emulation = $value;
    }

    public function withBody($body): self
    {
        $body = $this->_toJson($body);
        return parent::withBody($body);
    }

    protected function _toJson($content)
    {
        if (!function_exists('json_encode') || $this->use_emulation)
            return $this->_encodeEmulation($content);

        $json_string = json_encode($content, $this->encodingOptions);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new lmbException(json_last_error_msg());
        }

        return $json_string;
    }

    protected function _encodeEmulation($values)
    {
        if (is_null($values)) return '[]';
        if ($values === false) return 'false';
        if ($values === true) return 'true';
        if (is_scalar($values)) {
            if (is_float($values)) {
                // Always use "." for floats.
                return floatval(str_replace(",", ".", strval($values)));
            }

            if (is_string($values)) {
                static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
                return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $values) . '"';
            } else
                return $values;
        }
        $isList = true;
        for ($i = 0, reset($values); $i < count($values); $i++, next($values)) {
            if (key($values) !== $i) {
                $isList = false;
                break;
            }
        }
        $result = array();
        if ($isList) {
            foreach ($values as $v) $result[] = json_encode($v);
            return '[' . join(',', $result) . ']';
        } else {
            foreach ($values as $k => $v) $result[] = json_encode($k) . ':' . json_encode($v);
            return '{' . join(',', $result) . '}';
        }
    }

}
