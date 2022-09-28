<?php
namespace limb\cms\src;

use limb\filter_chain\src\lmbFilterChain;
use limb\core\src\lmbHandle;
use limb\web_app\src\exception\lmbErrorHandler;

class lmbCmsApplication extends lmbFilterChain
{
  function __construct()
  {
      (new lmbErrorHandler(dirname(__FILE__) . '/../template/server_error.html'))->register();

      $this->registerFilter(new lmbHandle('limb\dbal\src\filter\lmbAutoDbTransactionFilter'));
      $this->registerFilter(new lmbHandle('limb\web_app\src\filter\lmbSessionStartupFilter'));
      $this->registerFilter(new lmbHandle('limb\cms\src\filter\lmbCmsRequestDispatchingFilter'));
      $this->registerFilter(new lmbHandle('limb\web_app\src\filter\lmbResponseTransactionFilter'));
      $this->registerFilter(new lmbHandle('limb\cms\src\filter\lmbCmsAccessPolicyFilter'));
      $this->registerFilter(new lmbHandle('limb\web_app\src\filter\lmbActionPerformingFilter'));
      $this->registerFilter(new lmbHandle('limb\web_app\src\filter\lmbViewRenderingFilter'));
  }
}
