<?php

namespace limb\Cms\src\Controllers\Admin;

use limb\web_app\src\Controllers\LmbController;
use limb\ActiveRecord\lmbActiveRecord;
use limb\Cms\src\lmbCmsTreeBrowser;
use limb\Cms\src\Model\lmbCmsNode;
use limb\Core\Exception\lmbException;
use limb\Toolkit\lmbToolkit;

class AdminTreeController extends LmbController
{
    function doCreateNode($request)
    {
        $this->useForm('node_form');
        $this->setFormDatasource($request);

        if ($request->hasPost()) {
            $class_name = $request->get('class_name') ? $request->get('class_name') : 'lmbCmsNode';
            $node = new $class_name();

            $this->_importAndSave($request, $node);
        } else
            $request->set('class_name', 'lmbCmsNode');
    }

    function doEditNode($request)
    {
        $node = lmbActiveRecord::findById(lmbCmsNode::class, $request->get('id'));
        $this->useForm('node_form');
        $this->setFormDatasource($request);

        if ($request->hasPost())
            $this->_importAndSave($request, $node);
        else {
            $request->merge($node->export());
            $request->set('controller_name', $node->getControllerName());
        }
    }

    protected function _importAndSave($request, $node)
    {
        $node->import($request);

        $node->validate($this->error_list);

        if ($this->error_list->isValid()) {
            $node->saveSkipValidation();
            $this->closePopup();
        }
    }

    function doDelete($request)
    {
        if ($request->hasPost() && $request->get('delete')) {
            foreach ($request->getArray('ids') as $id) {
                $node = lmbActiveRecord::findById('lmbCmsNode', $id);
                $node->destroy();
            }
            $this->closePopup();
        }
    }

    function doSavePriority($request)
    {
        $priority = $request->get('priority');

        if (!is_array($priority) || !sizeof($priority))
            throw new lmbException('"priority" request param should be an array!');

        foreach ($priority as $id => $value) {
            $node = new lmbCmsNode($id);
            $node->setPriority($value);
            $node->save();
        }

        $this->closePopup();
    }

    function doMove($request)
    {
        if ($parent_id = $request->get('id')) {
            $parent_node = new lmbCmsNode($parent_id);
            $request->set('parent', $parent_node);
        }

        $this->useForm('tree_form');
        $this->setFormDatasource($request);

        if ($request->hasPost() && $request->get('move')) {
            if (!$parent_id = $request->get('parent_id')) {
                $parent_node = lmbCmsNode::findByPath('/');
                $parent_id = $parent_node->id;
            }

            foreach ($request->getArray('ids') as $id) {
                $tree = lmbToolkit::instance()->getCmsTree();
                $tree->moveNode($id, $parent_id);
            }
            $this->closePopup();
        }
    }

    function doProcessCommand($request)
    {
        $resource_type = $request->get('Type');
        $current_folder = $request->get('CurrentFolder');
        $command = $request->get('Command');

        $browser = new lmbCmsTreeBrowser();
        $browser->setCurrentFolderPath($current_folder);

        $this->_setXmlHeaders();

        $xml = '<?xml version="1.0" encoding="utf-8" ?>';
        $xml .= '<Connector command="' . $command . '" resourceType="' . $resource_type . '">';
        $xml .= '<CurrentFolder path="' . $current_folder . '" url="/" />';

        $xml .= '<Folders>' . $browser->renderFolders() . '</Folders>';
        $xml .= '<Files></Files>';

        $xml .= '</Connector>';

        return $xml;
    }

    protected function _setXmlHeaders()
    {
        response()
            ->addHeader('Expires: Mon, 26 Jul 1997 05:00:00 GMT')
            ->addHeader('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT')
            ->addHeader('Cache-Control: no-store, no-cache, must-revalidate')
            ->addHeader('Cache-Control: post-check=0, pre-check=0', false)
            ->addHeader('Pragma: no-cache')
            ->addHeader('Content-Type:text/xml; charset=utf-8');
    }
}
