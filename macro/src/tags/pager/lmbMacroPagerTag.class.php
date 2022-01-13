<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\macro\src\tags\pager;

use limb\macro\src\compiler\lmbMacroRuntimeWidgetTag;

/**
 * @tag pager
 * @package macro
 * @version $Id$
 */
class lmbMacroPagerTag extends lmbMacroRuntimeWidgetTag
{
  protected $widget_class_name = 'limb\macro\src\tags\pager\lmbMacroPagerHelper';

  protected function _generateContent($code)
  {
    $pager = $this->getRuntimeVar();

    if ($total_items = $this->getEscaped('total_items'))
      $code->writePhp("{$pager}->setTotalItems({$total_items});\n");

    if ($items = $this->getEscaped('items'))
      $code->writePhp("{$pager}->setItemsPerPage({$items});\n");

    if($this->findChildByClass('limb\macro\src\tags\pager\lmbMacroPagerElipsesTag'))
    {
      $code->writePhp("{$pager}->useElipses();\n");

      if ($this->has('pages_in_middle'))
      {
        $pages_in_middle = $this->getEscaped('pages_in_middle');
        $code->writePhp("{$pager}->setPagesInMiddle({$pages_in_middle});\n");
      }

      if ($this->has('pages_in_sides'))
      {
        $pages_in_sides = $this->getEscaped('pages_in_sides');
        $code->writePhp("{$pager}->setPagesInSides((int){$pages_in_sides});\n");
      }

      if ($this->has('pages_in_sides_start'))
      {
        $pages_in_sides_start = $this->getEscaped('pages_in_sides_start');
        $code->writePhp("{$pager}->setPagesInSidesStart((int){$pages_in_sides_start});\n");
      }

      if ($this->has('pages_in_sides_finish'))
      {
        $pages_in_sides_finish = $this->getEscaped('pages_in_sides_finish');
        $code->writePhp("{$pager}->setPagesInSidesFinish((int){$pages_in_sides_finish});\n");
      }
    }
    else
    {
      $code->writePhp("{$pager}->useSections();\n");

      if ($pages_per_section = $this->getEscaped('pages_per_section'))
        $code->writePhp("{$pager}->setPagesPerSection({$pages_per_section});\n");
    }

    $code->writePhp("{$pager}->prepare();\n");
    $code->writePhp("\$has_more_than_one_page = {$pager}->hasMoreThanOnePage();\n");

    $this->_generatePagerVariables($code, $pager);

    parent :: _generateContent($code);
  }

  protected function _generateWidget($code)
  {
    parent :: _generateWidget($code);

    $pager = $this->getRuntimeVar();
  }

  protected function _generatePagerVariables($code, $pager)
  {
     $code->writePhp("\$total_items = {$pager}->getTotalItems();\n");
     $code->writePhp("\$current_page = {$pager}->getCurrentPage();\n");
     $code->writePhp("\$total_pages = {$pager}->getTotalPages();\n");
     $code->writePhp("\$items_per_page = {$pager}->getItemsPerPage();\n");
     $code->writePhp("\$begin_item_number = {$pager}->getCurrentPageBeginItem();\n");
     $code->writePhp("\$end_item_number = {$pager}->getCurrentPageEndItem();\n");
  }
}

