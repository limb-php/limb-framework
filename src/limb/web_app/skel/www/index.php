<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

require_once(dirname(__FILE__) . '/../lmb-setup.php');

use limb\web_app\skel\src\LimbApplication;

$application = new LimbApplication();
$application
    ->process(request())
    ->send();
