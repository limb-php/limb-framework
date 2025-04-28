<?php

namespace limb\Cms\src\Controllers\Admin;

use limb\ActiveRecord\lmbActiveRecord;
use limb\Cms\src\Model\lmbCmsTextBlock;
use limb\Core\lmbCollection;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class TextBlockController extends lmbAdminObjectController
{
    protected $_form_name = 'object_form';
    protected $_object_class_name = lmbCmsTextBlock::class;
    public array $blocks;
    public $items;

    /** @return ResponseInterface|void */
    public function doDisplay($request)
    {
        $this->items = lmbActiveRecord::find(lmbCmsTextBlock::class);

        $this->blocks = $this->_getBlocks();
    }

    protected function _getBlocks()
    {
        $blocks = lmbCollection::toFlatArray($this->items, 'identifier');

        $result = array();
        foreach ($this->toolkit->getConf('text_blocks') as $identifier => $default_properties) {
            if (isset($blocks[$identifier])) {
                $item = $blocks[$identifier];
                $item['exists'] = true;
            } else {
                $item = new lmbCmsTextBlock();
                $item->import($default_properties);
                $item->setIdentifier($identifier);
                $item['exists'] = false;
            }

            $result[$identifier] = $item;
        }

        return $result;
    }

    /** @return ResponseInterface|void */
    public function doEdit(RequestInterface $request)
    {
        if (!$this->item = lmbCmsTextBlock::findOneByIdentifier($request->getAttribute('id')))
            $this->forwardTo404();

        $this->useForm($this->_form_name);
        $this->setFormDatasource($this->item);

        if ($request->hasPost())
        {
            $this->_import($request);

            $this->_validate($request);

            if ($this->error_list->isValid()) {
                $this->_update($request);

                return $this->_endDialog();
            }
        }
    }

    /** @return ResponseInterface|void */
    function doPreview($request)
    {
        if (!$this->item = lmbCmsTextBlock::findOneByIdentifier($request->get('id')))
            return $this->forwardTo404();
    }
}
