<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\cms\cases\request;

use tests\cms\cases\lmbCmsTestCase;
use limb\cms\src\request\lmbCmsDocumentRequestDispatcher;
use limb\web_app\src\request\lmbRoutes;
use limb\cms\src\model\lmbCmsDocument;
use limb\toolkit\src\lmbToolkit;

class lmbCmsDocumentRequestDispatcherTest extends lmbCmsTestCase
{

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

    $request = $toolkit->getRequest();
    $request->withUri( $request->getUri()->withPath('/news') );

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
  	$document = $this->_createDocument('news', lmbCmsDocument::findRoot());
    $document->setIsPublished(1);
    $document->save();

    $result = $this->_createDispatcher();

    $this->assertEquals('document', $result['controller']);
    $this->assertEquals('item', $result['action']);
    $this->assertEquals($document->getId(), $result['id']);
  }
}
