<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_agent;

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
