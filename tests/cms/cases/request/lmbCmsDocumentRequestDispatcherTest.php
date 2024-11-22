<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\cms\cases\request;

use limb\cms\src\model\lmbCmsDocument;
use limb\cms\src\Net\request\lmbCmsDocumentRequestDispatcher;
use limb\net\src\lmbHttpRequest;
use limb\toolkit\src\lmbToolkit;
use limb\web_app\src\request\lmbRoutes;
use tests\cms\cases\lmbCmsTestCase;

class lmbCmsDocumentRequestDispatcherTest extends lmbCmsTestCase
{

    protected $tables_to_cleanup = array('lmb_cms_document');

    function _createDispatcher()
    {
        $toolkit = lmbToolkit::instance();

        $config_array = [
            [
                'path' => '/:controller/:action',
                'defaults' => ['action' => 'display']
            ]
        ];
        $routes = new lmbRoutes($config_array);

        $toolkit->setRoutes($routes);

        $request = new lmbHttpRequest('https://localhost/news');

        $dispatcher = new lmbCmsDocumentRequestDispatcher();
        $result = $dispatcher->dispatch($request);

        return $result;
    }

    function testDispatch_NotFoundInDb()
    {
        $this->assertNull($this->_createDispatcher());
    }

    function testDispatch_FoundInDb()
    {
        $root = lmbCmsDocument::findRoot();

        $document = $this->_createDocument('news', $root);
        $document->setIsPublished(1);
        $document->save();

        $result = $this->_createDispatcher();

        $this->assertEquals('document', $result['controller']);
        $this->assertEquals('item', $result['action']);
        $this->assertEquals($document->getId(), $result['id']);
    }
}
