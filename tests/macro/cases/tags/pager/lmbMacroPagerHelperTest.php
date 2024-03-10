<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\tags\pager;

use PHPUnit\Framework\TestCase;
use limb\macro\src\tags\pager\lmbMacroPagerHelper;

class lmbMacroPagerHelperTest extends TestCase
{
    protected $pager_id;
    protected $pager;
    protected $old_get;
    protected $old_server;

    function setUp(): void
    {
        $this->old_get = $_GET;
        $this->old_server = $_SERVER;

        $_SERVER['REQUEST_URI'] = 'http://test.com';
        $_GET = array();

        $this->pager_id = 'test_pager';
        $this->pager = new lmbMacroPagerHelper($this->pager_id);
    }

    function tearDown(): void
    {
        $_GET = $this->old_get;
        $_SERVER = $this->old_server;
    }

    function testPrepare()
    {
        $this->pager->setCurrentPage(2);
        $this->pager->setItemsPerPage(10);
        $this->pager->setPagesPerSection(5);
        $this->pager->setTotalItems(100);

        $this->pager->prepare();

        $this->assertEquals($this->pager->getCurrentPage(), 2);
        $this->assertFalse($this->pager->isDisplayedPage());
        $this->assertEquals($this->pager->getPage(), 1);
        $this->assertEquals($this->pager->getTotalPages(), 10);
        $this->assertEquals($this->pager->getPagesPerSection(), 5);
        $this->assertTrue($this->pager->hasMoreThanOnePage());
        $this->assertEquals($this->pager->getSectionBeginPage(), 1);
        $this->assertEquals($this->pager->getSectionEndPage(), 5);
        $this->assertTrue($this->pager->hasNext());
        $this->assertTrue($this->pager->hasPrev());
        $this->assertEquals($this->pager->getCurrentPageBeginItem(), 11);
        $this->assertEquals($this->pager->getCurrentPageEndItem(), 20);
        $this->assertEquals($this->pager->getCurrentPageOffset(), 10);
    }

    function testGettingCurrentPageWithGetIfCurrentPageWasNotSet()
    {
        $this->pager->setItemsPerPage(10);
        $this->pager->setPagesPerSection(5);
        $this->pager->setTotalItems(100);

        $_GET[$this->pager_id] = 2;

        $this->pager->prepare();

        $this->assertEquals($this->pager->getCurrentPage(), 2);
        $this->assertFalse($this->pager->isDisplayedPage());
        $this->assertEquals($this->pager->getPage(), 1);
        $this->assertEquals($this->pager->getTotalPages(), 10);
        $this->assertEquals($this->pager->getPagesPerSection(), 5);
        $this->assertTrue($this->pager->hasMoreThanOnePage());
        $this->assertEquals($this->pager->getSectionBeginPage(), 1);
        $this->assertEquals($this->pager->getSectionEndPage(), 5);
        $this->assertTrue($this->pager->hasNext());
        $this->assertTrue($this->pager->hasPrev());
        $this->assertEquals($this->pager->getCurrentPageBeginItem(), 11);
        $this->assertEquals($this->pager->getCurrentPageEndItem(), 20);
        $this->assertEquals($this->pager->getCurrentPageOffset(), 10);
    }

    function testTotalItemsZero()
    {
        $this->pager->setCurrentPage(2);
        $this->pager->setItemsPerPage(10);
        $this->pager->setPagesPerSection(5);
        $this->pager->setTotalItems(0);

        $this->pager->prepare();

        $this->assertEquals($this->pager->getCurrentPage(), 1);
        $this->assertEquals($this->pager->getPage(), 1);
        $this->assertTrue($this->pager->isDisplayedPage());
        $this->assertEquals($this->pager->getTotalPages(), 1);
        $this->assertFalse($this->pager->hasMoreThanOnePage());
        $this->assertEquals($this->pager->getSectionBeginPage(), 1);
        $this->assertEquals($this->pager->getSectionEndPage(), 1);
        $this->assertFalse($this->pager->hasNext());
        $this->assertFalse($this->pager->hasPrev());
        $this->assertEquals($this->pager->getCurrentPageBeginItem(), 0);
        $this->assertEquals($this->pager->getCurrentPageEndItem(), 0);
        $this->assertEquals($this->pager->getCurrentPageOffset(), 0);
    }

    function testNextPage()
    {
        $this->pager->setCurrentPage(2);
        $this->pager->setTotalItems(40);
        $this->pager->setItemsPerPage(10);
        $this->pager->setPagesPerSection(5);

        $this->pager->prepare();

        $this->assertEquals($this->pager->getPage(), 1);

        $this->assertTrue($this->pager->nextPage());
        $this->assertTrue($this->pager->isValid());

        $this->assertEquals($this->pager->getPage(), 2);
    }

    function testNextPageOutOfBounds()
    {
        $this->pager->setCurrentPage(2);
        $this->pager->setTotalItems(40);
        $this->pager->setItemsPerPage(10);

        $this->pager->prepare();

        $this->assertTrue($this->pager->nextPage());
        $this->assertTrue($this->pager->isValid());

        $this->assertTrue($this->pager->nextPage());
        $this->assertTrue($this->pager->isValid());

        $this->assertTrue($this->pager->nextPage());
        $this->assertTrue($this->pager->isValid());

        $this->assertFalse($this->pager->nextPage());
        $this->assertFalse($this->pager->isValid());
    }

    function testSectionNumbers()
    {
        $this->pager->setCurrentPage(2);
        $this->pager->setTotalItems(40);
        $this->pager->setItemsPerPage(3);
        $this->pager->setPagesPerSection(10);

        $this->pager->prepare();

        $this->pager->nextPage();

        $this->assertEquals($this->pager->getSection(), 1);
        $this->assertEquals($this->pager->getSectionBeginPage(), 1);
        $this->assertEquals($this->pager->getSectionEndPage(), 10);
    }

    function testSectionNumbersRightBound()
    {
        $this->pager->setCurrentPage(2);
        $this->pager->setTotalItems(40);
        $this->pager->setItemsPerPage(10);// 4 pages total
        $this->pager->setPagesPerSection(10);

        $this->pager->prepare();

        $this->pager->nextPage();

        $this->assertEquals($this->pager->getSection(), 1);
        $this->assertEquals($this->pager->getSectionBeginPage(), 1);
        $this->assertEquals($this->pager->getSectionEndPage(), 4);
    }

    function testNextSection()
    {
        $this->pager->setCurrentPage(2);
        $this->pager->setTotalItems(40);
        $this->pager->setItemsPerPage(5);
        $this->pager->setPagesPerSection(2);

        $this->pager->prepare();

        $this->assertTrue($this->pager->nextSection());
        $this->assertTrue($this->pager->nextSection());
        $this->assertTrue($this->pager->nextSection());
        $this->assertFalse($this->pager->nextSection());
    }

    function testGetFirstPageUri()
    {
        $_GET['p1'] = ' wow ';
        $_GET['p2'] = array('3' => 'yo');

        $this->pager->prepare();

        $uri = $this->pager->getPageUri(1);

        $this->assertEquals($uri, 'http://test.com?p1=+wow+&p2[3]=yo');
    }

    function testGetFirstPageUriNoQuery()
    {
        $this->pager->prepare();

        $uri = $this->pager->getPageUri(1);

        $this->assertEquals($uri, 'http://test.com');
    }

    function testGetPageUri()
    {
        $_GET['p1'] = 'wow';
        $_GET['p2'] = array('3' => ' yo ');

        $this->pager->prepare();

        $uri = $this->pager->getPageUri(2);

        $this->assertEquals($uri, 'http://test.com?p1=wow&p2[3]=+yo+&test_pager=2');
    }

    function testGetPrevSectionUri()
    {
        $this->pager->setCurrentPage(3);
        $this->pager->setTotalItems(60);
        $this->pager->setItemsPerPage(10);
        $this->pager->setPagesPerSection(2);

        $this->pager->prepare();

        $this->pager->nextPage();

        $uri = $this->pager->getSectionUri();

        $this->assertEquals($uri, 'http://test.com?test_pager=2');
        $this->assertEquals($this->pager->getSectionBeginPage(), 1);
        $this->assertEquals($this->pager->getSectionEndPage(), 2);
    }

    function testGetNextSectionUri()
    {
        $this->pager->setCurrentPage(3);
        $this->pager->setTotalItems(60);
        $this->pager->setItemsPerPage(10);
        $this->pager->setPagesPerSection(2);

        $this->pager->prepare();

        for ($i = 0; $i < 5; $i++)
            $this->pager->nextPage();

        $uri = $this->pager->getSectionUri(2);

        $this->assertEquals($uri, 'http://test.com?test_pager=5');
        $this->assertEquals($this->pager->getSectionBeginPage(), 5);
        $this->assertEquals($this->pager->getSectionEndPage(), 6);
    }
}
