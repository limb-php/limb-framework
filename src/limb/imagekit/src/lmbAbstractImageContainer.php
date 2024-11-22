<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\imagekit\src;

/**
 * Abstract image container
 *
 * @package imagekit
 * @version $Id: lmbAbstractImageContainer.php 7486 2009-01-26 19:13:20Z
 */
abstract class lmbAbstractImageContainer
{

    protected $output_type = '';

    function setOutputType($type)
    {
        $this->output_type = $type;
    }

    function getOutputType()
    {
        return $this->output_type;
    }

    abstract function load($file_name, $type = '');

    abstract function save($file_name = null);

}
