<?php
namespace limb\macro\src\tags\core;

use limb\macro\src\compiler\lmbMacroTag;

/**
 * class RenderTag.
 * @tag render
 * @restrict_self_nesting
 */
class RenderTag extends lmbMacroTag
{
  protected function _generateContent($code_writer)
  {
    $function = $this->get('function');
    list($keys, $vals) = $this->attributesIntoArgs();
    if( ($key = array_search('$function', $keys)) !== false )
    {
      unset($vals[$key]);
    }

    if( $function )
    {
      $view_var = $code_writer->generateVar();

      if( !empty( $vals ) )
        $code_writer->writePHP($view_var . " = call_user_func('" . $function . "', " . implode(', ', $vals) . ");");
      else
        $code_writer->writePHP($view_var . " = call_user_func('" . $function . "', array());");
      $code_writer->writePHP(" echo {$view_var}->render(); ");

      parent::_generateContent($code_writer);
    }
  }
}
