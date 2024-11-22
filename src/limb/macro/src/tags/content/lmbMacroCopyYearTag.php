<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\tags\content;

use limb\macro\src\compiler\lmbMacroTag;

/**
 * Output copyright years depends current year
 * class limb\macro\src\tags\content\lmbMacroCopyYearTag
 *
 * @tag copy_year
 * @req_attributes start_year
 * @package macro
 * @version $Id$
 */
class lmbMacroCopyYearTag extends lmbMacroTag
{
    protected function _generateContent($code_writer)
    {
        $start_year = $this->get('start_year');

        $code_writer->writePhp("echo $start_year, (date('Y') != $start_year) ? '&ndash;' . date('Y') : '';\n");
    }
}

