<?php
namespace limb\macro\src;

interface lmbMacroTemplateLocatorInterface
{
  function __construct(lmbMacroConfig $config);
  function locateSourceTemplate($file_name);
  function locateCompiledTemplate($file_name);
}
