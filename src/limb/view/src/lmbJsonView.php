<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\view\src;

use limb\view\src\exception\lmbJsonViewException;
use limb\toolkit\src\lmbToolkit;

/**
 * class lmbJsonView.
 *
 * @package view
 * @version $Id$
 */
class lmbJsonView extends lmbView
{
    protected $use_emulation = false;

    // Encode <, >, ', &, and " characters in the JSON, making it also safe to be embedded into HTML.
    // 15 === JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
    public const DEFAULT_ENCODING_OPTIONS = 15;

    protected $encodingOptions = self::DEFAULT_ENCODING_OPTIONS;

    function __construct($vars = array())
    {
        parent::__construct(null, $vars);
    }

    static function create($vars = []): self
    {
        return new self($vars);
    }

    function useEmulation($value)
    {
        $this->use_emulation = $value;
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

    function render()
    {
        lmbToolkit::instance()->getResponse()->addHeader('Content-type: application/json');

        if (!function_exists('json_encode') || $this->use_emulation)
            $data = $this->_encodeEmulation($this->getVariables());
        else
            $data = json_encode($this->getVariables(), $this->encodingOptions);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new lmbJsonViewException(json_last_error_msg());
        }

        return $data;
    }
}
