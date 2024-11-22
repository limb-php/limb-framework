<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\web_spider\cases;

use PHPUnit\Framework\TestCase;
use limb\net\src\lmbUri;
use limb\web_spider\src\lmbUriNormalizer;

class lmbUriNormalizerTest extends TestCase
{
    protected $normalizer;

    function setUp(): void
    {
        $this->normalizer = new lmbUriNormalizer();
    }

    function testNormalizeStripAnchor()
    {
        $links = array(new lmbUri('index.html?a=1&b=2#test'));

        $uri = $this->normalizer->process($links[0]);
        $this->assertEquals($uri, new lmbUri('index.html?a=1&b=2'));
    }

    function testNormalizeStripQuery()
    {
        $links = array(
            new lmbUri('index.html?a=1&b=2'),
            new lmbUri('https://test.com/page1.html?whatever'),
            new lmbUri('https://test.com/page2.html?PHPSESSID=id&a=1')
        );

        $this->normalizer->stripQueryItem('PHPSESSID');
        $this->normalizer->stripQueryItem('whatever');

        $uri = $this->normalizer->process($links[0]);
        $this->assertEquals($uri, new lmbUri('index.html?a=1&b=2'));

        $uri = $this->normalizer->process($links[1]);
        $this->assertEquals($uri, new lmbUri('https://test.com/page1.html'));

        $uri = $this->normalizer->process($links[2]);
        $this->assertEquals($uri, new lmbUri('https://test.com/page2.html?a=1'));
    }
}
