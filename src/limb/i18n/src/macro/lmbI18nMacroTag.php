<?php
/*
* Limb PHP Framework
*
* @link http://limb-project.com
* @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
* @license    LGPL http://www.gnu.org/copyleft/lesser.html
*/

namespace limb\i18n\src\macro;

use limb\macro\src\compiler\lmbMacroTag;
use limb\macro\src\lmbMacroException;

/**
 * @tag i18n
 * @aliases __
 * @req_attributes text
 * @forbid_end_tag
 */
class lmbI18nMacroTag extends lmbMacroTag
{

    function preParse($compiller)
    {
        if (!$this->has('text')) throw new lmbMacroException('tag {{i18n}} required attribute "text"');
        parent::preParse($compiller);
    }

    protected function _generateContent($code_writer)
    {
        $code_writer->writePHP('
    echo limb\i18n\src\lmbI18n::translate(\'' . $this->get('text') . '\',\'' . ($this->has('domain') ? $this->get('domain') : 'default') . '\');
        ');
    }

}
