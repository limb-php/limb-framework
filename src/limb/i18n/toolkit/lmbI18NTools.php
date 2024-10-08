<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\i18n\toolkit;

use limb\fs\toolkit\lmbFsTools;
use limb\toolkit\lmbAbstractTools;
use limb\i18n\locale\lmbLocale;
use limb\core\lmbEnv;
use limb\i18n\translation\lmbQtDictionaryBackend;
use limb\config\lmbCachedIni;
use limb\config\lmbIni;

lmbEnv::setor('LIMB_LOCALE_INCLUDE_PATH', 'i18n/locale;limb/i18n/i18n/locale');

/**
 * class lmbI18NTools.
 *
 * @package i18n
 * @version $Id: lmbI18NTools.php 8150 2010-03-26 07:58:18Z
 */
class lmbI18NTools extends lmbAbstractTools
{
    protected $current_locale;
    protected $locale_objects = array();
    protected $dictionaries = array();
    protected $dict_backend;

    static function getRequiredTools()
    {
        return [
            lmbFsTools::class
        ];
    }

    function getDictionaryBackend()
    {
        if (!is_object($this->dict_backend)) {
            $this->dict_backend = new lmbQtDictionaryBackend();
            if (lmbEnv::get('LIMB_VAR_DIR')) {
                $this->dict_backend->setCacheDir(lmbEnv::get('LIMB_VAR_DIR'));
                $this->dict_backend->useCache();
            }
        }

        return $this->dict_backend;
    }

    function setDictionaryBackend($backend)
    {
        $this->dict_backend = $backend;
    }

    function getLocale()
    {
        if (!$this->current_locale)
            $this->current_locale = 'en_US';

        return $this->current_locale;
    }

    function setLocale($locale)
    {
        $this->current_locale = $locale;
    }

    function getLocaleObject($locale = null)
    {
        if (!$locale)
            $locale = $this->toolkit->getLocale();

        if (!isset($this->locale_objects[$locale]))
            $this->locale_objects[$locale] = $this->toolkit->createLocaleObject($locale);

        return $this->locale_objects[$locale];
    }

    function addLocaleObject($obj, $locale = null)
    {
        if (!$locale)
            $locale = $obj->getLocaleString();

        $this->locale_objects[$locale] = $obj;
    }

    function createLocaleObject($locale)
    {
        $file = $this->toolkit->findFileByAlias($locale . '.ini', lmbEnv::get('LIMB_LOCALE_INCLUDE_PATH'), 'i18n_locale');

        if (lmbEnv::has('LIMB_VAR_DIR'))
            return new lmbLocale($locale, new lmbCachedIni($file, lmbEnv::get('LIMB_VAR_DIR') . '/locale/'));
        else
            return new lmbLocale($locale, new lmbIni($file));
    }

    function getDictionary($locale, $domain)
    {
        if (!isset($this->dictionaries[$locale . '@' . $domain])) {
            $backend = $this->toolkit->getDictionaryBackend();
            $this->dictionaries[$locale . '@' . $domain] = $backend->load($locale, $domain);
        }

        return $this->dictionaries[$locale . '@' . $domain];
    }

    function setDictionary($locale, $domain, $dict)
    {
        $this->dictionaries[$locale . '@' . $domain] = $dict;
    }

    function translate($text, $arg1 = null, $arg2 = null)
    {
        $locale = $this->toolkit->getLocale();

        $domain = 'default';
        $attributes = null;

        if (is_array($arg1)) {
            $attributes = $arg1;
            if (is_string($arg2))
                $domain = $arg2;
        } elseif (is_string($arg1))
            $domain = $arg1;

        if ($dict = $this->toolkit->getDictionary($locale, $domain))
            return $dict->translate($text, $attributes);
        else
            return $text;
    }
}
