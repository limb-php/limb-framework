<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_app\src\request;

use Psr\Http\Message\RequestInterface;

/**
 * interface lmbRequestDispatcherInterface.
 *
 * @package web_app
 * @version $Id: lmbRequestDispatcherInterface.php 7486 2009-01-26 19:13:20Z
 */
interface lmbRequestDispatcherInterface
{
    /**
     * @param RequestInterface $request
     * @return array
     *
     * return assoc-array with keys:
     *  controller - controller name
     *  action - action name
     */

    function dispatch(RequestInterface $request): array;
}
