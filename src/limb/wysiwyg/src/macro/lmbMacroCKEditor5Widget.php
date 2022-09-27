<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright © 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\wysiwyg\src\macro;

/**
 * @package wysiwyg
 * @version $Id$
 */
class lmbMacroCKEditor5Widget extends lmbMacroBaseWysiwygWidget
{
  function renderWysiwyg()
  {
    $this->_initWysiwyg();

    $this->_renderEditor();
  }

  protected function _renderEditor()
  {
    $config = array();
    if($this->_helper->getOption('Config'))
      $config	= $this->_helper->getOption('Config');

    if($this->_helper->getOption('basePath'))
      $ckeditorPath	= $this->_helper->getOption('basePath');
    else
      $ckeditorPath = '/shared/wysiwyg/ckeditor5/';

    $attr = "";
    /*foreach ($this->textareaAttributes as $key => $val) {
      $attr.= " " . $key . '="' . str_replace('"', '&quot;', $val) . '"';
    }*/

    echo "<textarea class=\"ckEditor5\" name=\"" . $this->getAttribute('name') . "\"" . $attr . ">" . htmlspecialchars($this->getValue()) . "</textarea>\n";
    echo "<script type=\"text/javascript\" src=\"" . $ckeditorPath . "ckeditor.js\"></script>\n";
    if( isset($config['customConfig']) )
      echo "<script type=\"text/javascript\" src=\"" . $config['customConfig'] . "\"></script>\n";
    echo "<script>
      var CKEditod5Config = (typeof(loadCKEditod5Config) != 'undefined') ? loadCKEditod5Config() : {} ;
      ClassicEditor
          .create( document.querySelector( '.ckEditor5[name=" . $this->getAttribute('name') . "]' ), CKEditod5Config )
          .catch( error => {
              console.error( error );
          } );
     </script>";
  }
}
