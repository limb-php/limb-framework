<?php
namespace limb\wysiwyg\src;

use limb\toolkit\src\lmbToolkit;
use limb\core\src\exception\lmbException;

class lmbWysiwygConfigurationHelper
{
  protected $_config_name = 'wysiwyg';
  protected $_profile_name;
  protected $_wysiwyg_types = array(
    'fckeditor' => array(
      'macro' => array(
        'class' => 'limb\wysiwyg\src\macro\lmbMacroFCKEditorWidget'
      ),
    ),
    'ckeditor' => array(
      'macro' => array(
        'class' => 'limb\wysiwyg\src\macro\lmbMacroCKEditorWidget'
      ),
    ),
    'ckeditor5' => array(
      'macro' => array(
        'class' => 'limb\wysiwyg\src\macro\lmbMacroCKEditor5Widget'
      ),
    ),
    'tinymce' => array(
      'macro' => array(
        'class' => 'limb\wysiwyg\src\macro\lmbMacroTinyMCEWidget'
      ),
    ),
  );

  function getWysiwygConfigOption($name)
  {
    return lmbToolkit::instance()->getConf($this->_config_name)->get($name);
  }

  function getProfileName()
  {
    if($this->_profile_name)
      return ($this->_profile_name);
    else
      return $this->getWysiwygConfigOption('default_profile');
  }

  function setProfileName($name)
  {
    $this->_profile_name = $name;
  }

  function getOption($name)
  {
    $profile_options = $this->getWysiwygConfigOption($this->getProfileName());
    if(isset($profile_options[$name]))
      return $profile_options[$name];
  }

  function getMacroWidgetInfo()
  {
    $wysiwyg_type = $this->getOption('type');

    if(!isset($this->_wysiwyg_types[$wysiwyg_type]))
      throw new lmbException('Wysiwyg type "'.$wysiwyg_type.'" not supported',array('type' => $wysiwyg_type));

    return $this->_wysiwyg_types[$wysiwyg_type]['macro'];
  }

  function getTwigWidgetInfo()
  {
    $wysiwyg_type = $this->getOption('type');

    if(!isset($this->_wysiwyg_types[$wysiwyg_type]))
      throw new lmbException('Wysiwyg type "'.$wysiwyg_type.'" not supported',array('type' => $wysiwyg_type));

    return $this->_wysiwyg_types[$wysiwyg_type]['twig'];
  }
}