<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers\oci;

/**
 * abstract class lmbOciLob.
 *
 * @package dbal
 * @version $Id: lmbOciLob.php 7486 2009-01-26 19:13:20Z
 */
abstract class lmbOciLob
{
    protected $value;

    function __construct($value)
    {
        $this->value = $value;
    }

    abstract function getNativeType();

    abstract function getEmptyExpression();

    abstract function getDescriptorType();

    function read()
    {
        return $this->value;
    }
}
