<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\active_record\src;

/**
 * class lmbARNotFoundException.
 *
 * @package active_record
 * @version $Id: lmbARNotFoundException.php 7486 2009-01-26 19:13:20Z
 */
class lmbARNotFoundException extends lmbARException
{
    protected $id;
    protected $class;

    function __construct($class, $id)
    {
        $this->id = $id;
        $this->class = $class;

        parent::__construct("Can't load ActiveRecord '" . $class . "' with id '$id'");
    }

    function getId()
    {
        return $this->id;
    }

    function getClass()
    {
        return $this->class;
    }
}
