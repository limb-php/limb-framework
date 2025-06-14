<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\web_app\cases\plain\fetcher;

use PHPUnit\Framework\TestCase;
use limb\Core\lmbCollection;
use limb\Core\lmbCollectionDecorator;
use limb\web_app\src\fetcher\lmbFetcher;
use limb\Core\lmbSet;
use limb\Core\Exception\lmbException;

class TestingDatasetDecorator extends lmbCollectionDecorator
{
    protected $prefix1;
    protected $prefix2;
    public $sort_params;

    function setPrefix1($prefix)
    {
        $this->prefix1 = $prefix;
    }

    function setPrefix2($prefix)
    {
        $this->prefix2 = $prefix;
    }

    function sort($sort_params)
    {
        $this->sort_params = $sort_params;
    }

    function current()
    {
        $record = parent::current();
        $data = $record->export();
        $data['full'] = $this->prefix1 . $data['name'] . '-' . $data['job'] . $this->prefix2;
        $processed_record = new lmbSet();
        $processed_record->import($data);
        return $processed_record;
    }
}

class TestingFetcher extends lmbFetcher
{
    public $use_dataset = array();

    protected function _createDataSet()
    {
        return $this->use_dataset;
    }
}

class lmbFetcherTest extends TestCase
{

    function testFetchCreateDatasetUsesScalarValue()
    {
        $fetcher = new TestingFetcher();
        $fetcher->use_dataset = 'blah';
        $dataset = $fetcher->fetch();

        $dataset->rewind();
        $this->assertFalse($dataset->valid());
    }

    function testFetchCreateDatasetUsesArray()
    {
        $fetcher = new TestingFetcher();
        $fetcher->use_dataset = array(array('name' => 'John', 'job' => 'Carpenter'),
            array('name' => 'Mike', 'job' => 'Fisher'));
        $dataset = $fetcher->fetch();

        $dataset->rewind();
        $this->assertTrue($dataset->valid());
        $record = $dataset->current();
        $this->assertEquals('John', $record->get('name'));
    }

    function testFetchCreateDatasetUsesObject()
    {
        $fetcher = new TestingFetcher();
        $fetcher->use_dataset = new lmbCollection(array(array('name' => 'John', 'job' => 'Carpenter'),
            array('name' => 'Mike', 'job' => 'Fisher')));
        $dataset = $fetcher->fetch();

        $dataset->rewind();
        $this->assertTrue($dataset->valid());
        $record = $dataset->current();
        $this->assertEquals('John', $record->get('name'));
    }

    function testAddDecoratorWithParams()
    {
        $fetcher = new TestingFetcher();
        $fetcher->use_dataset = new lmbCollection(array(
            array('name' => 'John', 'job' => 'Carpenter'),
            array('name' => 'Mike', 'job' => 'Fisher')));
        $fetcher->addDecorator(TestingDatasetDecorator::class, array(
            'prefix1' => 'PrefixA_',
            'prefix2' => '_PrefixB'));
        $dataset = $fetcher->fetch();

        $dataset->rewind();
        $this->assertTrue($dataset->valid());
        $record = $dataset->current();
        $this->assertEquals('PrefixA_John-Carpenter_PrefixB', $record->get('full'));
    }

    function testSetOrder()
    {
        $fetcher = new TestingFetcher();
        $fetcher->use_dataset = new lmbCollection(array(array('name' => 'John', 'job' => 'Carpenter'),
            array('name' => 'Mike', 'job' => 'Fisher')));
        $fetcher->addDecorator(TestingDatasetDecorator::class);
        $fetcher->setOrder('title=ASC,name,last_name=DESC');

        $dataset = $fetcher->fetch();

        $this->assertEquals($dataset->sort_params, array(
            'title' => 'ASC',
            'name' => 'ASC',
            'last_name' => 'DESC')
        );
    }

    function testExtractOrderPairsFromStringSimpleCase()
    {
        $order = lmbFetcher::extractOrderPairsFromString('title=DESC,name=ASC');
        $this->assertEquals($order, array(
            'title' => 'DESC',
            'name' => 'ASC')
        );
    }

    function testExtractOrderPairsFromStringSimpleRandom()
    {
        $order = lmbFetcher::extractOrderPairsFromString('title=rand()');
        $this->assertEquals($order, array('title' => 'RAND()'));
    }

    function testExtractOrderPairsFromStringSimpleError()
    {
        try {
            lmbFetcher::extractOrderPairsFromString('title=error');
            $this->assertTrue(false);
        } catch (lmbException $e) {
            $this->assertMatchesRegularExpression('/Wrong order type/', $e->getMessage());
        }
    }
}
