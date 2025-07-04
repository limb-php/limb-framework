<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\session\src;

interface lmbSessionStorageInitializerInterface
{

    function init($options = []): lmbSessionStorageInterface;

}
