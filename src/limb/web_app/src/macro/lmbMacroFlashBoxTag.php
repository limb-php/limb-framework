<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_app\src\macro;

use limb\macro\src\compiler\lmbMacroTag;

/**
 * @tag flashbox
 * @package macro
 * @version $Id$
 */
class lmbMacroFlashBoxTag extends lmbMacroTag
{
    protected function _generateContent($code_writer)
    {
        if ($this->get('as')) {
            $to = $this->get('as');
        }
        if ($this->get('to')) {
            $to = $this->get('to');
        } else
            $to = '$flashbox';


        $method = $code_writer->beginMethod('__flashbox_container');

        $code_writer->writePHP($to . '=$this->toolkit->getFlashBox()->getUnifiedList();');
        $code_writer->writePHP('$this->toolkit->getFlashBox()->reset();');

        parent::_generateContent($code_writer);

        $code_writer->endMethod();

        $code_writer->writePHP('$this->' . $method . '();');
    }
}
