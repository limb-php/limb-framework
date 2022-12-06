<?php
/*
 *
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\web_spider\cases;

use limb\web_spider\src\lmbContentTypeFilter;
use PHPUnit\Framework\TestCase;

class lmbContentTypeFilterTest extends TestCase
{
  var $filter;

  function setUp(): void
  {
    $this->filter = new lmbContentTypeFilter();
  }

  function testFilterAcceptedContentTypes()
  {
    $this->filter->allowContentType('html/text');
    $this->filter->allowContentType('xml/text');

    $this->assertTrue($this->filter->canPass('html/text'));
    $this->assertFalse($this->filter->canPass('image/png'));
    $this->assertTrue($this->filter->canPass('html/text'));
  }
}
