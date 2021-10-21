<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\macro\src\tags\tree;

use limb\macro\src\tags\form\lmbMacroSelectTag;

/**
 * analog for {{select}} tag to creating <select> tags for generating tree
 * @tag tree_select
 * @package macro
 * @version $Id$
 */

class lmbMacroTreeSelectTag extends lmbMacroSelectTag
{
  public function preParse($compiler)
  {
    //$this->widget_include_file = 'limb/macro/src/tagr/tree/lmbMacroTreeSelectWidget.class.php';
    $this->widget_class_name = 'limb\macro\src\tag\tree\lmbMacroTreeSelectWidget';

    parent :: preParse($compiler);
  }

  function _generateContent($code_writer)
  {
    $select = $this->getRuntimeVar();

    if( !$this->has('first_option') )
      $title = 'Выбрать родителя';
    else
      $title = $this->get('first_option');

    $code_writer->writePHP("{$select}->prependToOptions('0', array('title' => '$title', 'path' => null, 'level' => null));\n");

    parent :: _generateContent($code_writer);
  }
}