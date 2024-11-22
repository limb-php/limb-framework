<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_agent\src;

/**
 * Web agent kit
 *
 * @package web_agent
 * @version $Id: lmbWebAgentKit.php 40 2007-10-04 15:52:39Z CatMan $
 */
class lmbWebAgentKit
{

    static function createRequest($req = 'socket')
    {
        $class = 'lmb' . ucfirst($req) . 'WebAgentRequest';

        $class_path = 'limb\\web_agent\\src\\request\\' . $class;

        return new $class_path;
    }

}
