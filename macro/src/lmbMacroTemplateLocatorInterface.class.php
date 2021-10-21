<?php
namespace limb\macro\src;

use limb\macro\src\lmbMacroConfig;

interface lmbMacroTemplateLocatorInterface
{
  function __construct(lmbMacroConfig $config);
  function locateSourceTemplate($file_name);
  function locateCompiledTemplate($file_name);
}
