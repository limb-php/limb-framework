<?php
/*
 * Limb PHP Framework
 *
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
