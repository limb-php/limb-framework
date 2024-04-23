<?php

namespace limb\cms\src\Controllers;

use limb\cms\src\Controllers\Admin\lmbObjectController;
use limb\cms\src\model\lmbCmsDocument;

class DocumentController extends lmbObjectController
{
    protected $_object_class_name = lmbCmsDocument::class;

    function doItem($request)
    {
        if (!$this->item = $this->_getObjectByRequestedId($request))
            return $this->forwardTo404();

        $mod_date = intval($this->item->utime);
        $expires = 43200; // half of day
        $last_modified = gmdate('D, d M Y H:i:s', $mod_date) . ' GMT';
        $expire_date = gmdate('D, d M Y H:i:s', gmmktime() + $expires) . ' GMT';

        return response()
            ->addHeader('Last-Modified: ' . $last_modified)
            ->addHeader('Expires: ' . $expire_date)
            ->addHeader('Cache-Control: max-age=' . $expires . ', must-revalidate'); //half day

    }

}
