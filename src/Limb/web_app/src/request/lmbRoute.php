<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\web_app\src\request;

use limb\Core\lmbObject;

/**
 * class lmbRoute.
 *
 * @package web_app
 * @version $Id: lmbRoute.php 8086 2010-01-22 01:32:51Z
 */
class lmbRoute extends lmbObject
{
    protected $name = null;

    protected $prefix = '';
    protected $path = '';
    protected $defaults = [];
    protected $requirements = [];

    protected $url_filter = null;

    protected $dispatch_filter = null; // ? same as rewriter
    protected $rewriter = null;

}
