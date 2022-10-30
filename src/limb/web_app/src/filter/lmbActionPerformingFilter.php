<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace limb\web_app\src\filter;

use limb\filter_chain\src\lmbInterceptingFilterInterface;
use limb\toolkit\src\lmbToolkit;
use limb\core\src\exception\lmbException;
use limb\view\src\lmbView;

/**
 * class lmbActionPerformingFilter.
 *
 * @package web_app
 * @version $Id: lmbActionPerformingFilter.php 7486 2009-01-26 19:13:20Z
 */
class lmbActionPerformingFilter implements lmbInterceptingFilterInterface
{
  function run($filter_chain)
  {
      $dispatched = lmbToolkit::instance()->getDispatchedController();
      if(!is_object($dispatched))
        throw new lmbException('Request is not dispatched yet! lmbDispatchedRequest not found in lmbToolkit!');

      $response = lmbToolkit::instance()->getResponse();

      $result = $dispatched->performAction();
      if( $result ) {
          if( is_a($result, lmbView::class) ) {
              lmbToolkit::instance()->setView($result);
          }
          else {
              lmbToolkit::instance()->getResponse()->write($result);
          }
      }

      $filter_chain->next();
  }
}
