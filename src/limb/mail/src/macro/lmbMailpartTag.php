<?php
/*
* Limb PHP Framework
*
* @license    LGPL http://www.gnu.org/copyleft/lesser.html
*/

namespace limb\mail\src\macro;

use limb\macro\src\compiler\lmbMacroTag;
use limb\macro\src\lmbMacroException;

/**
 * @tag mailpart
 * @aliases
 * @req_attributes name
 * @restrict_self_nesting
 */
class lmbMailpartTag extends lmbMacroTag
{
    function preParse($compiler): void
    {
        if (!$this->has('name'))
            throw new lmbMacroException('Tag {{mailpart}}, required attribute "name"');

        parent::preParse($compiler);
    }

    function _generateBeforeContent($code_writer)
    {
        $code_writer->writeHTML('<mailpart name="' . $this->get('name') . '"><![CDATA[');
    }

    function _generateAfterContent($code_writer)
    {
        $code_writer->writeHTML(']]></mailpart>');
    }
}
