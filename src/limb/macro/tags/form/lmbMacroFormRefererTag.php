<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\tags\form;

use limb\macro\compiler\lmbMacroTag;

/**
 * @tag form:referer
 * @forbid_end_tag
 * @package macro
 * @version $Id$
 */
class lmbMacroFormRefererTag extends lmbMacroTag
{
    function _generateContent($code_writer)
    {
        $ref = $code_writer->generateVar();

        $code_writer->writePHP($ref . ' = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "";' . "\n");

        $code_writer->writePHP("if($ref)");
        $code_writer->writePHP('echo "<input type=\'hidden\' name=\'referer\' value=\'' . $ref . '\'>";');
    }
}

