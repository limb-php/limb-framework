<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

use limb\web_agent\src\lmbWebAgentValues;

/**
 * @package web_agent
 * @version $Id: lmbWebAgentValuesTest.class.php 40 2007-10-04 15:52:39Z CatMan $
 */
class lmbWebAgentValuesTest extends TestCase {

  function testBuildQuery()
  {
    $arr = array('test' => 'val', 'test1' => 'val1');
  	$vals = new lmbWebAgentValues($arr);

    $this->assertEquals($vals->buildQuery(), http_build_query($arr));
  }

}
