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

    $config_array = array(
        array('path' => '/:controller/:action',
              'defaults' => array('action' => 'display')
        )
    );
    $routes = new lmbRoutes($config_array);

    $toolkit->setRoutes($routes);

    $toolkit->getRequest()->getUri()->withPath('/news'); // fix

    $dispatcher = new lmbCmsDocumentRequestDispatcher();

    $result = $dispatcher->dispatch($toolkit->getRequest());

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

    $this->assertEquals($result['controller'], 'document');
    $this->assertEquals($result['action'], 'item');
    $this->assertEquals($result['id'], $document->getId());
  }
}
