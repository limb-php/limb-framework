<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/wysiwyg/src/wact/lmbWysiwygComponent.class.php');

@define('LIMB_CKEDITOR_DIR', 'limb/wysiwyg/lib/CKeditor/');

/**
 * class lmbCKEditorComponent.
 *
 * @package wysiwyg
 * @version $Id: lmbCKEditorComponent.class.php 7486 2009-01-26 19:13:20Z 3d-max $
 */
class lmbCKEditorComponent extends lmbWysiwygComponent
{
  var $dir = '';

  function renderContents()
  {
    $this->renderEditor();
  }

  function renderEditor()
  {
    include_once(LIMB_CKEDITOR_DIR . '/ckeditor.php');

    $editor = new CKeditor($this->getAttribute('name')) ;
    $config = $this->_setEditorParameters($editor);

    $editor->editor($this->getAttribute('name'), $this->getValue(), $config);
  }

  function _setEditorParameters($editor)
  {
    if($this->_helper->getOption('basePath'))
      $editor->basePath	= $this->_helper->getOption('basePath');
    else
      $editor->basePath = '/shared/wysiwyg/ckeditor/';

    $config = array();
    if($this->_helper->getOption('Config'))
      $config	= $this->_helper->getOption('Config');

    return $config;
  }
}


