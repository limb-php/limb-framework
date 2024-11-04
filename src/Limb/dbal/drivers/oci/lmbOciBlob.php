<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\drivers\oci;

/**
 * class lmbOciBlob.
 *
 * @package dbal
 * @version $Id: lmbOciBlob.php 7486 2009-01-26 19:13:20Z
 */
class lmbOciBlob extends lmbOciLob
{
    function getDescriptorType()
    {
        return OCI_D_LOB;
    }

    function getEmptyExpression()
    {
        return 'EMPTY_BLOB()';
    }

    function getNativeType()
    {
        return OCI_B_BLOB;
    }
}
