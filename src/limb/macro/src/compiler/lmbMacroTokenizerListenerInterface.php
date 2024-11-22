<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src\compiler;

/**
 * interface lmbMacroTokenizerListenerInterface.
 *
 * @package macro
 * @version $Id$
 */
interface lmbMacroTokenizerListenerInterface
{
    function startElement($tag_name, $attrs);

    function endElement($tag_name);

    function emptyElement($tag_name, $attrs);

    function characters($data);

    function php($data);

    function unexpectedEOF($data);

    function invalidEntitySyntax($data);

    function invalidAttributeSyntax($data);
}
