<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

require_once(dirname(__FILE__) . '/../lmb-setup.php');

use limb\web_app\skel\src\LimbApplication;

$application = new LimbApplication();
$application
    ->process(request())
    ->send();
