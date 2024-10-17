<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\tags\content;

use limb\macro\compiler\lmbMacroTag;

/**
 * Output copyright years depends on current year
 * class limb\macro\tags\content\lmbMacroCopyYearTag
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
