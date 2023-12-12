<?php

namespace Tests\cms\cases\model;

use Tests\cms\cases\lmbCmsTestCase;
use limb\cms\src\model\lmbCmsSeo;
use limb\net\src\lmbUri;

class lmbCmsSeoTest extends lmbCmsTestCase
{
    function setUp(): void
    {
        parent::setUp();

        $items = $this->_getItemsArray();

        foreach ($items as $item) {
            $seo_item = new lmbCmsSeo();
            $seo_item->setUrl($item['url']);
            $seo_item->setTitle($item['title']);
            $seo_item->setDescription($item['description']);
            $seo_item->setKeywords($item['keywords']);
            $seo_item->save();
        }
    }

    public function testMetaFieldsWithoutEndSlash()
    {
        $items = $this->_getItemsArray();
        foreach ($items as $item) {
            $meta = lmbCmsSeo::getMetaForUrl(new lmbUri($item['url']));

            $this->assertEquals($meta->get('title'), $item['title']);
            $this->assertEquals($meta->get('keywords'), $item['keywords']);
            $this->assertEquals($meta->get('description'), $item['description']);
        }
    }

    public function testMetaFieldsWithEndSlash()
    {
        $items = $this->_getItemsArray();
        foreach ($items as $item) {
            $meta = lmbCmsSeo::getMetaForUrl(new lmbUri($item['url']));

            $this->assertEquals($meta->get('title'), $item['title']);
            $this->assertEquals($meta->get('keywords'), $item['keywords']);
            $this->assertEquals($meta->get('description'), $item['description']);
        }
    }

    public function testMetaFieldFirstEqual()
    {
        $items = $this->_getItemsArray();
        foreach ($items as $item) {
            $meta = lmbCmsSeo::getMetaForUrl(new lmbUri($item['url'] . '/test/test'));

            $this->assertEquals($meta->get('title'), $item['title']);
            $this->assertEquals($meta->get('keywords'), $item['keywords']);
            $this->assertEquals($meta->get('description'), $item['description']);
        }
    }

    protected function _getItemsArray()
    {
        $items = array(array(
            'url' => '/',
            'title' => 'Main Page',
            'keywords' => 'Keywords Main Page',
            'description' => 'Description Main Page'
        ),
            array(
                'url' => '/photogallery',
                'title' => 'Photogallery',
                'keywords' => 'Keywords Photogallery',
                'description' => 'Description Photogallery'
            ),
            array(
                'url' => '/photogallery/folder1',
                'title' => 'Title Photogallery Folder1',
                'keywords' => 'Keywords Photogallery Folder1',
                'description' => 'Description Photogallery Folder1'
            ),
            array(
                'url' => '/photogallery/folder1/folder2',
                'title' => 'Title Photogallery Folder 1 Folder 2',
                'keywords' => 'Keywords Photogallery Folder 1 Folder 2',
                'description' => 'Description Photogallery Folder 1 Folder 2'
            ),
        );
        return $items;
    }
}
