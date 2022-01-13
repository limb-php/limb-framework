<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\macro\src\tags\form;

use limb\macro\src\tags\form\lmbMacroFormTagElement;
use limb\macro\src\tags\form\lmbMacroOptionTag;

/**
 * Macro analog for html <select> tag
 * @tag select
 * @package macro
 * @version $Id$
 */
class lmbMacroSelectTag extends lmbMacroFormTagElement
{
  protected $html_tag = 'select';

  function preParse($compiler)
  {
    if ($this->getBool('multiple'))
    {
      $this->widget_class_name = 'limb\macro\src\tags\form\lmbMacroMultipleSelectWidget';

      if (!$this->has('name'))
      {
        if ($this->has('id') )
          $this->set('name', $this->get('id').'[]'); // Note - appends [] to id value
        else
          $this->raiseRequiredAttribute('name');
      }

      if (!is_integer(strpos($this->get('name'), '[]')))
      {
        $this->raise('Array brackets "[]" required in name attribute, e.g. name="foo[]"',
                     array('name' => $this->get('name')));
      }
    }
    elseif(!isset($this->widget_class_name))
    {
      $this->widget_class_name = 'limb\macro\src\tags\form\lmbMacroSingleSelectWidget';
    }

    // always has closing tag
    $this->has_closing_tag = true;

    parent :: preParse($compiler);
  }

  protected function _generateBeforeOpeningTag($code)
  {
    $select = $this->getRuntimeVar();
    // passing specified variable as a datasource to form widget
    if($this->has('options'))
    {
      $options = $this->get('options');
      $code->writePHP("{$select}->setOptions({$options});\n");
      //$this->remove('options');
    }
  }

  function _generateContent($code_writer)
  {
    $select = $this->getRuntimeVar();

    foreach($this->getChildren() as $option_tag)
    {
      if(!$option_tag instanceof lmbMacroOptionTag)
        continue;

      $value = $option_tag->get('value');
      $prepend = $option_tag->getBool('prepend');

      $text = $code_writer->generateVar();

      $code_writer->writePHP("ob_start();\n");
      $option_tag->generateNow($code_writer);
      $code_writer->writePHP("{$text} = ob_get_contents();\n");
      $code_writer->writePHP("ob_end_clean();\n");

      if($prepend)
        $code_writer->writePHP("{$select}->prependToOptions('{$value}', {$text});\n");
      else
        $code_writer->writePHP("{$select}->addToOptions('{$value}', {$text});\n");

      if($option_tag->has('selected'))
        $code_writer->writePHP("{$select}->addToDefaultSelection('{$value}');\n");
    }

    $code_writer->writePHP("{$select}->renderOptions();\n");
  }
}
