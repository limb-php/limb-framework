<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\Toolkit;

/**
 * Interface for defining toolkit tools that extends lmbToolkit
 * @see lmbToolkit
 * @package toolkit
 * @version $Id: lmbToolkitToolsInterface.php 7486 2009-01-26 19:13:20Z
 */
interface lmbToolkitToolsInterface
{
    public function bootstrap();

    static function getRequiredTools();

    /**
     * @return array Array of method names with reference to itself, something like array('getUser' => $this, 'getTree' => $this)
     */
    function getToolsSignatures();
}
