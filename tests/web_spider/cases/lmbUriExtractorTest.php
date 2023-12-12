<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\web_spider\cases;

use PHPUnit\Framework\TestCase;
use limb\net\src\lmbUri;
use limb\web_spider\src\lmbUriExtractor;

class lmbUriExtractorTest extends TestCase
{
    var $extractor;

    function setUp(): void
    {
        $this->extractor = new lmbUriExtractor();
    }

    function testFindLinks()
    {
        $content = <<< EOD
<html>
<head>
</head>
<body>
<a href="https://test.com">""  - link</a>

<a href='https://test.com'>'' - link</a>

<a href='https://test2.com?wow=1&bar=4'>'' - link with query</a>

<a href=https://test2.com>no quotes link</a>

<a href='/root/news/3' class='title-site2'>link with attributes in atag</a>

<a href='/root/news/4'>

multiline
 link
 </a>

</body>
</html>
EOD;

        $links = $this->extractor->extract($content);
        $this->assertEquals(6, sizeof($links));

        $this->assertEquals($links[0], new lmbUri('https://test.com'));
        $this->assertEquals($links[1], new lmbUri('https://test.com'));
        $this->assertEquals($links[2], new lmbUri('https://test2.com?wow=1&bar=4'));
        $this->assertEquals($links[3], new lmbUri('https://test2.com'));
        $this->assertEquals($links[4], new lmbUri('/root/news/3'));
        $this->assertEquals($links[5], new lmbUri('/root/news/4'));
    }
}
