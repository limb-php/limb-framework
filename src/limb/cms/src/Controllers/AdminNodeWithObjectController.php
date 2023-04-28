<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\cms\src\Controllers;

use \limb\web_app\src\Controllers\LmbController;
use limb\cms\src\model\lmbCmsNode;
use limb\core\src\exception\lmbException;
use limb\active_record\src\lmbActiveRecord;

/**
 * abstract class AdminNodeWithObjectController.
 *
 * @package cms
 * @version $Id$
 */
abstract class AdminNodeWithObjectController extends LmbController
{
  protected $_form_name = 'object_form';
  protected $_controller_name = '';
  protected $_node_class_name = lmbCmsNode::class;
  protected $_object_class_name = '';
  protected $_generate_identifier = false;

  protected $node = null;
  protected $item = null;

  function __construct()
  {
    parent :: __construct();

    if(!$this->_object_class_name || !$this->_controller_name)
      throw new lmbException('Object class name or(and) controller name is not specified');
  }

  function doCreate()
  {
    $this->node = new $this->_node_class_name();
    $this->item = new $this->_object_class_name();

    $this->useForm($this->_form_name);
    $this->setFormDatasource($this->request);

    if($this->request->hasPost())
    {
      $this->node->setControllerName($this->_controller_name);
      $this->node->setObject($this->item);
      $this->item->setNode($this->node);
      $this->_import();

      if($this->_generate_identifier || $this->request->get('auto_identifier'))
        $this->node->setIdentifier(lmbCmsNode::generateIdentifier($this->request->get('parent')));

      $this->_validateAndSave(true);
    }
    else
    {
      $this->_initCreateForm();
    }
  }

  function doEdit()
  {
    $this->node = lmbActiveRecord :: findById($this->_node_class_name, $this->request->getInteger('id'), false);
    if(!$this->node)
      return $this->forwardTo404();

    $this->item = $this->node->getObject();
    $this->useForm($this->_form_name);
    $this->setFormDatasource($this->request);

    if($this->request->hasPost())
    {
      $this->_import();
      $this->_validateAndSave(false);
    }
    else
    {
      $this->_initEditForm();
    }
  }

  protected function _import()
  {
    $this->node->import($this->request->export());
    $this->item->import($this->request->export());
  }

  protected function _validateAndSave($is_create = false)
  {
    $this->_onBeforeValidate();
    $this->node->validate($this->error_list);
    $this->item->validate($this->error_list);
    $this->_onAfterValidate();

    if($this->error_list->isValid())
    {
      if($is_create)
        $this->_onBeforeCreate();
      else
        $this->_onBeforeEdit();

      $this->_onBeforeSave();
      $this->node->saveSkipValidation();
      //$this->item->saveSkipValidation();
      $this->_onAfterSave();

      if($is_create)
        $this->_onAfterCreate();
      else
        $this->_onAfterEdit();

      $this->closePopup();
    }
  }

  protected function _initEditForm()
  {
    $this->request->merge($this->node->export());
    $this->request->merge($this->item->export());
    $this->request->set('node', $this->node);
    $this->request->set('item', $this->item);
  }

  function performPublishCommand()
  {
    $ids = $this->request->get('ids');
    if(!is_array($ids) || !count($ids))
      $this->closePopup();

    foreach($ids as $id)
    {
      $item = new $this->_object_class_name((int)$id);
      $item->setIsPublished(true);
      $item->save();
    }

    $this->closePopup();
  }

  function performUnpublishCommand()
  {
    $ids = $this->request->get('ids');
    if(!is_array($ids) || !count($ids))
      $this->closePopup();

    foreach($ids as $id)
    {
      $item = new $this->_object_class_name((int)$id);
      $item->setIsPublished(false);
      $item->save();
    }

    $this->closePopup();
  }

  function doDelete()
  {
    if($this->request->hasPost())
      $this->_onBeforeDelete();

    if($this->request->get('delete'))
    {
      foreach($this->request->getArray('ids') as $id)
      {
        $node = lmbActiveRecord::findById(lmbCmsNode::class, (int)$id);
        $node->destroy();
      }

      $this->_onAfterDelete();
      $this->closePopup();
    }
  }

  protected function _initCreateForm() {}
  protected function _onBeforeSave() {}
  protected function _onAfterSave() {}
  protected function _onBeforeCreate() {}
  protected function _onAfterCreate() {}
  protected function _onBeforeEdit() {}
  protected function _onAfterEdit() {}
  protected function _onBeforeDelete() {}
  protected function _onAfterDelete() {}
  protected function _onBeforeValidate() {}
  protected function _onAfterValidate() {}
}
