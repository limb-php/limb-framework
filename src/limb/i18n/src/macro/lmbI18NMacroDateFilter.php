<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\i18n\src\macro;

use limb\macro\src\compiler\lmbMacroFilter;

/**
 * Filter i18n_date for macro templates
 * @filter i18n_date
 * @package i18n
 * @version $Id$
 */
class lmbI18NMacroDateFilter extends lmbMacroFilter
{
    var $date;

    function preGenerate($code)
    {
        $code->registerInclude(dirname(__FILE__) . '/filters.inc.php');
        parent::preGenerate($code);
    }

    function getValue()
    {
        $params = "array(";
        foreach ($this->params as $key => $value) {
            $params .= $value . ",";
        }
        $params .= ")";
        return 'lmb_i18n_date_filter(' . $params . ', ' . $this->base->getValue() . ')';
    }
}
