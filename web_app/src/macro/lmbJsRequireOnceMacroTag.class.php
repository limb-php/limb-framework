<?php
namespace limb\web_app\src\macro;

use limb\web_app\src\macro\lmbFileVersionMacroTag;

/**
 * @tag js:require_once
 * @aliases js_once
 * @req_attributes src
 * @forbid_end_tag
 */  

class lmbJsRequireOnceMacroTag extends lmbFileVersionMacroTag
{  
  static protected $_writes = array();

  protected function _generateContent($code)
  {
    $file = $this->get('src');
    if(isset(self :: $_writes[$code->getClass()][$file]))
      return;
    self :: $_writes[$code->getClass()][$file] = 1;

    if(!$this->has('type'))
      $this->set('type', 'js');
    parent :: _generateContent($code);
  }
}
