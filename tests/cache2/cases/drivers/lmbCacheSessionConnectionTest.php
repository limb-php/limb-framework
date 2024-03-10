<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\cache2\cases\drivers;

require_once(dirname(__FILE__) . '/../.setup.php');

class lmbCacheSessionConnectionTest extends lmbCacheConnectionTestCase
{
    function __construct()
    {
        $this->dsn = 'session:';
        parent::__construct();
    }

    function testGetWithTtl_differentThread()
    {
        //session not share between threads
    }
}
