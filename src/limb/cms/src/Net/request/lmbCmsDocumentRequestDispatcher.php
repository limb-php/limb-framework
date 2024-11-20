<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cms\src\Net\request;

use limb\cms\src\model\lmbCmsDocument;
use limb\core\src\exception\lmbException;
use limb\toolkit\src\lmbToolkit;
use limb\web_app\src\request\lmbRequestDispatcherInterface;
use Psr\Http\Message\RequestInterface;

/**
 * class lmbDbRequestDispatcher.
 *
 * @package web_app
 * @version $Id: lmbRoutesRequestDispatcher.php 7114 2008-07-12 14:59:47Z
 */
class lmbCmsDocumentRequestDispatcher implements lmbRequestDispatcherInterface
{
    function dispatch(RequestInterface $request): array
    {
        $path = $request->getUri()->getPath();
        if ($path === '/')
            return [];

        if (!$document = lmbCmsDocument::findByUri($path))
            return [];

        if (!$document->getIsPublished()) {
            if (lmbToolkit::instance()->isWebAppDebugEnabled())
                throw new lmbException('Page not published');
            else
                return [];
        }

        return array(
            'controller' => 'document',
            'action' => 'item',
            'id' => $document->getId()
        );
    }
}
