<?php
namespace limb\optimization\src\controller;

use src\controller\AdminObjectController;

class AdminSeoController extends AdminObjectController
{
  protected $_object_class_name = 'MetaInfo';

  /* */
  public function doDisplay()
  {
    $this->items = MetaInfo :: findForAdmin( array('sort' => array('url' => 'ASC')) );
  }

  public function doCreateEditByUrl()
  {
    $this->item = MetaInfo :: findByUrl( $url = $this->request->get('url') );
    if( !$this->item )
    {
      $this->setTemplate('admin_seo/create.phtml');

      $this->item = new MetaInfo();
      $this->item->setUrl( $url );

      $this->_onCreate();

      $this->useForm($this->_form_name);
      $this->setFormDatasource($this->item);

      if($this->request->hasPost())
      {
        $this->_import();
        $this->_validateAndSave(true);
      }
      else
      {
        $this->item->import($this->request);
        $this->_initCreateForm();
      }
    }
    else
    {
      $this->setTemplate('admin_seo/edit.phtml');

      $this->_onEdit();

      $this->useForm($this->_form_name);
      $this->setFormDatasource($this->item);

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
  }
}

