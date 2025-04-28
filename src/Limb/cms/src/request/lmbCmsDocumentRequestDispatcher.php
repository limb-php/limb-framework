<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\Cms\src\Request;

use limb\web_app\src\Request\lmbRequestDispatcherInterface;
use Psr\Http\Message\RequestInterface;
use limb\Cms\src\Model\lmbCmsDocument;
use limb\Core\Exception\lmbException;
use limb\Toolkit\lmbToolkit;

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
