<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2009 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    cms
 */

namespace limb\cms\src\Controllers;

use limb\cms\src\model\lmbCmsDocument;
use limb\active_record\src\lmbActiveRecord;
use limb\dbal\src\criteria\lmbSQLCriteria;
use limb\core\src\exception\lmbException;

class AdminDocumentController extends lmbAdminObjectController
{
    protected $_object_class_name = lmbCmsDocument::class;

    function doDisplay($request)
    {
        if (!$id = $request->getInteger('id')) {
            $this->is_root = true;
            $criteria = new lmbSQLCriteria('parent_id > 0');
            $criteria->addAnd(new lmbSQLCriteria('level = 1'));
            $this->item = lmbCmsDocument::findRoot();
        } else {
            $this->is_root = false;
            if (!$this->item = $this->_getObjectByRequestedId($request))
                return $this->forwardTo404();
            $criteria = new lmbSQLCriteria('parent_id = ' . $this->item->getId());
        }

        $this->items = lmbActiveRecord::find($this->_object_class_name, array('criteria' => $criteria, 'sort' => array('priority' => 'ASC')));
        $this->_applySortParams($request);
    }

    function doPriority($request)
    {
        if ($request->has('parent_id'))
            $this->_changeItemsPriority($request, 'lmbCmsDocument', 'parent_id', $request->get('parent_id'));

        $this->_endDialog();
    }

    function doCreate($request)
    {
        if (!$this->parent = $this->_getObjectByRequestedId())
            $this->forwardTo404();

        $this->item = new $this->_object_class_name();

        $this->_onCreate($request);

        $this->useForm($this->_form_name);
        $this->setFormDatasource($this->item);

        if ($request->hasPost()) {
            $this->_import($request);
            $this->item->setParent($this->parent);
            $this->_validateAndSave($create = true);
        } else
            $this->_initCreateForm($request);
    }

    protected function _onBeforeImport($request)
    {
        $request->set('identifier', trim($request->get('identifier')));
        $request->set('title', trim($request->get('title')));
    }

    protected function _validateAndSave($request, $is_create = false)
    {
        try {
            parent::_validateAndSave($request, $is_create);
        } catch (lmbException $e) {
            $this->error_list->addError('Документ со значением поля "Идентификатор" уже существует на данном уровне вложения');
        }
    }

}
