<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\i18n\macro;

use limb\macro\compiler\lmbMacroFilter;
use limb\macro\lmbMacroException;

/**
 * Filter i18n_clip for macro templates
 * @filter i18n_clip
 * @package i18n
 * @version $Id$
 */
class lmbI18NMacroClipFilter extends lmbMacroFilter
{
    protected $suffix_var;
    protected $chunk_var;

    function preGenerate($code)
    {
        parent::preGenerate($code);

        $value = $this->base->getValue();

        if (count($this->params) > 2) {
            $this->suffix_var = $code->generateVar();
            $limit = $this->params[0];
            $offset = $this->params[1];
            $code->writePhp("{$this->suffix_var} = '';\n");
            $code->writePhp("if(limb\i18n\charset\lmbI18nString::strlen($value) > ($limit + $offset)) {$this->suffix_var} = " . $this->params[2] . ";\n");
        }
        if (count($this->params) > 3) {
            $this->chunk_var = $code->generateVar();
            $code->writePhp($this->chunk_var . ' = limb\i18n\charset\lmbI18nString::substr(' . $value . ',' . $this->params[1] . ',' . $this->params[0] . ');');
        }
    }

    function getValue()
    {
        $suffix = '';

        $value = $this->base->getValue();
        switch (count($this->params)) {
            case 1:
                return 'limb\i18n\charset\lmbI18nString::substr(' . $value . ',' . 0 . ',' . $this->params[0] . ')';
                break;
            case 2:
                return 'limb\i18n\charset\lmbI18nString::substr(' . $value . ',' . $this->params[1] . ',' . $this->params[0] . ')';
                break;
            case 3:
                return 'limb\i18n\charset\lmbI18nString::substr(' . $value . ',' . $this->params[1] . ',' . $this->params[0] . ') . ' . $this->suffix_var;
                break;
            case 4:
                $limit = $this->params[0];
                $offset = $this->params[1];
                $word_wrap = $this->params[3];

                if (strtoupper(substr($word_wrap, 0, 1)) != 'N')
                    return "(limb\i18n\charset\lmbI18nString::preg_match('~^(' . preg_quote({$this->chunk_var}) . '[^\s]*)~ism', limb\i18n\src\charset\lmbI18nString::substr($value, $offset), \$match) ? \$match[1] . {$this->suffix_var} : '')";
                else
                    return 'limb\i18n\charset\lmbI18nString::substr(' . $value . ',' . $offset . ',' . $limit . ')' . $suffix;
                break;
            default:
                throw new lmbMacroException('Wrong number of filter params(1..4)');
        }
    }
}
