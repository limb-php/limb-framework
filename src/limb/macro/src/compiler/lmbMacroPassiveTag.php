<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\compiler;

/**
 * class lmbMacroPassiveTag.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroPassiveTag extends lmbMacroTag
{

    function generate($code_writer)
    {
    }

    function generateNow($code_writer)
    {
        parent::generate($code_writer);
    }
}
