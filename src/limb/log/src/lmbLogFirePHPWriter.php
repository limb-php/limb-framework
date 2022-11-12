<?php
namespace limb\log\src;

use FirePHP;
use limb\net\src\lmbUri;

class lmbLogFirePHPWriter extends FirePHP implements lmbLogWriterInterface
{
  protected $check_client_extension;

  function __construct(lmbUri $dsn)
  {
    $this->check_client_extension = $dsn->getQueryItem('check_extension', 1);
  }

  function write(lmbLogEntry $entry)
  {
    return $this->fb($entry->asText());
  }

  function disableCheckClientExtension()
  {
    $this->check_client_extension = false;
  }

  function detectClientExtension()
  {
  	if($this->check_client_extension)
  	  return parent::detectClientExtension();
  	else
  	  return true;
  }

  function isClientExtensionCheckEnabled()
  {
  	return $this->check_client_extension;
  }
}
