<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\tags\pager;

use limb\macro\src\compiler\lmbMacroPassiveTag;

/**
 * Compile time component for elispses in a pager.
 * Elipses are sed to mark omitted page numbers outside of the
 * current range of the pager e.g. ...6 7 8... (the ... are the elipses)
 * @tag pager:elipses
 * @restrict_self_nesting
 * @parent_tag_class limb\macro\src\tags\pager\lmbMacroPagerListTag
 * @package macro
 * @version $Id$
 */
class lmbMacroPagerElipsesTag extends lmbMacroPassiveTag
{
}
