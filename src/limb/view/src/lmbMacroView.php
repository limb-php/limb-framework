<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\view\src;

use limb\core\src\exception\lmbException;
use limb\macro\src\lmbMacroTemplate;
use limb\toolkit\src\lmbToolkit;

/**
 * class lmbMacroView.
 *
 * @package view
 * @version $Id$
 */
class lmbMacroView extends lmbView
{
  protected $macro_template;

  function __construct($template_name, $vars = array())
  {
    $pos = strrpos($template_name, '.');
    if($pos === false)
    {
      $template_name .= '.phtml';
    }

    parent::__construct($template_name, $vars);
  }

  static function locateTemplateByAlias($alias)
  {
    $locator = lmbToolkit :: instance()->getMacroLocator();

    if($template_path = $locator->locateSourceTemplate($alias))
      return $template_path;
  }

  function render()
  {
    if($tpl = $this->_getMacroTemplate())
    {
      $this->_fillMacroTemplate($tpl);
      return $tpl->render();
    }
    else
    {
      throw new lmbException('Empty macro template');

    }
  }

  function reset()
  {
    parent :: reset();
    $this->macro_template = null;
  }

  function getMacroTemplate()
  {
    return $this->_getMacroTemplate();
  }

  protected function _getMacroTemplate()
  {
    if($this->macro_template)
      return $this->macro_template;

    if(!$path = $this->getTemplate())
      return null;

    $toolkit = lmbToolkit :: instance();
    $this->macro_template = new lmbMacroTemplate($path, $toolkit->getMacroConfig(), $toolkit->getMacroLocator());
    return $this->macro_template;
  }

  protected function _fillMacroTemplate($template)
  {
    foreach($this->getVariables() as $variable_name => $value)
      $template->set($variable_name, $value);

    foreach($this->forms_datasources as $form_id => $datasource)
      $template->set('form_' . $form_id . '_datasource', $datasource);

    foreach($this->forms_errors as $form_id => $error_list)
      $template->set('form_' . $form_id . '_error_list', $error_list->getArray());
  }
}
