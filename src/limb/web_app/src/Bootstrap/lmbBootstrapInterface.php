<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_app\src\Bootstrap;

/**
 * @package web_app
 */
interface lmbBootstrapInterface
{

    function bootstrap($request): void;


    function terminate(): void;

}
