<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\web_app\src\controller;

use limb\web_app\src\controller\LmbController;

/**
 * class NotFoundController.
 *
 * @package web_app
 * @version $Id: NotFoundController.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class NotFoundController extends LmbController
{
  function doDisplay()
  {
    $this->response->addHeader('HTTP/1.0 404 Not Found');
    $this->setTemplate($this->findTemplateByAlias('not_found'));
  }
}


