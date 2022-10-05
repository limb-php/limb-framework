<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

use limb\net\src\lmbUri;
use limb\web_spider\src\lmbUriNormalizer;

class lmbUriNormalizerTest extends TestCase
{
  var $normalizer;

  function setUp()
  {
    $this->normalizer = new lmbUriNormalizer();
  }

  function testNormalizeStripAnchor()
  {
    $links = array(new lmbUri('index.html?a=1&b=2#test'));

    $this->normalizer->process($links[0]);
    $this->assertEquals($links[0], new lmbUri('index.html?a=1&b=2'));
  }

  function testNormalizeStripQuery()
  {
    $links = array(new lmbUri('index.html?a=1&b=2'),
                   new lmbUri('http://test.com/page1.html?whatever'),
                   new lmbUri('http://test.com/page2.html?PHPSESSID=id&a=1'));

    $this->normalizer->stripQueryItem('PHPSESSID');
    $this->normalizer->stripQueryItem('whatever');

    $this->normalizer->process($links[0]);
    $this->assertEquals($links[0], new lmbUri('index.html?a=1&b=2'));

    $this->normalizer->process($links[1]);
    $this->assertEquals($links[1], new lmbUri('http://test.com/page1.html'));

    $this->normalizer->process($links[2]);
    $this->assertEquals($links[2], new lmbUri('http://test.com/page2.html?a=1'));
  }
}


